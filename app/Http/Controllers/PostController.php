<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/PostController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a single blog post.
     */
    public function show(string $slug)
    {
        // FIX 1: Do NOT cache the Eloquent model object.
        //         Serialising a model with loaded relationships into cache
        //         can fail silently or return a broken object on retrieval → 500.
        //         Fetch fresh on every request; only lightweight scalar data belongs in cache.
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['author', 'category', 'tags'])
            ->firstOrFail();

        // Persist reading_time so it is only computed once, not on every page load
        if (! $post->reading_time) {
            $post->reading_time = $this->calculateReadingTime($post->content);
            $post->saveQuietly(); // FIX 2: original code never saved back → recomputed every request
        }

        $this->incrementPostViews($post->id);

        // ✅ UPDATED: Changed 'approved' to 'is_approved'
        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)  // ← Changed from 'approved' to 'is_approved'
            ->with(['replies' => function ($query) {
                $query->where('is_approved', true)->orderBy('created_at', 'asc');  // ← Changed
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $relatedPosts = Cache::remember("related_{$post->id}", 3600, function () use ($post) {
            return Post::published()
                ->where('id', '!=', $post->id)
                ->where('category_id', $post->category_id)
                ->with(['author', 'category'])
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        return view('pages.news.article', compact('post', 'comments', 'relatedPosts'));

        // FIX 3: Removed the blanket try/catch that was converting every exception into
        //         a generic 500 abort() — making the real error completely invisible.
        //         Laravel's own exception handler logs errors in production and shows
        //         the full stack trace in debug mode, which is exactly what you need.
    }

    /**
     * Store a new comment.
     */
    public function storeComment(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->firstOrFail();

        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'nullable|email|max:255',
            'comment'   => 'required|string|min:5|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Basic spam check (use stripos — was strtolower+strpos, which is equivalent but verbose)
        foreach (['viagra', 'casino', 'porn', 'xxx', 'gambling'] as $keyword) {
            if (stripos($validated['comment'], $keyword) !== false) {
                return back()
                    ->with('error', 'Your comment contains inappropriate content.')
                    ->withInput();
            }
        }

        // ✅ UPDATED: Changed 'approved' to 'is_approved'
        Comment::create([
            'post_id'      => $post->id,
            'parent_id'    => $validated['parent_id'] ?? null,
            'user_id'      => auth()->id(),
            'name'         => $validated['name'],
            'email'        => $validated['email'] ?? null,
            'comment'      => $validated['comment'],
            'is_approved'  => false,  // ← Changed from 'approved' to 'is_approved'
            'ip_address'   => $request->ip(),
        ]);

        return back()->with('success', 'Your comment has been submitted and is awaiting moderation. Thank you!');
        // FIX 4: Removed redundant try/catch — Laravel handles ValidationException
        //         automatically (redirects back with errors). Catching it manually
        //         here was hiding real DB/config errors behind a generic message.
    }

    /**
     * Increment post views without triggering model events.
     */
    private function incrementPostViews(int $postId): void
    {
        try {
            DB::table('posts')->where('id', $postId)->increment('view_count');
        } catch (\Exception $e) {
            Log::warning('Failed to increment view count: ' . $e->getMessage());
        }
    }

    /**
     * Calculate reading time in minutes.
     */
    private function calculateReadingTime(?string $content): int
    {
        if (empty($content)) {
            return 1;
        }
        return max(1, (int) ceil(str_word_count(strip_tags($content)) / 200));
    }

    /**
     * Display comments for a post (AJAX endpoint).
     */
    public function getComments(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        // ✅ UPDATED: Changed 'approved' to 'is_approved'
        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)  // ← Changed from 'approved' to 'is_approved'
            ->with(['replies' => function ($query) {
                $query->where('is_approved', true)->orderBy('created_at', 'asc');  // ← Changed
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json($comments);
        }

        return back();
    }

    /**
     * Like/upvote a comment.
     */
    public function likeComment(Request $request, int $commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            $comment->increment('likes');

            return response()->json(['success' => true, 'likes' => $comment->fresh()->likes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to like comment'], 500);
        }
    }
}