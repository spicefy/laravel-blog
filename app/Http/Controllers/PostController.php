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
     * Display a single blog post
     */
    public function show(string $slug)
    {
        try {
            // Cache individual articles for 1 hour
            $post = Cache::remember("post_{$slug}", 3600, function () use ($slug) {
                $post = Post::where('slug', $slug)
                    ->published()
                    ->with(['author', 'category', 'tags'])
                    ->firstOrFail();
                
                // Ensure reading_time is set
                if (!$post->reading_time) {
                    $post->reading_time = $this->calculateReadingTime($post->content);
                }
                
                return $post;
            });

            // Increment views asynchronously using DB update (bypass cache)
            $this->incrementPostViews($post->id);

            // Get approved comments with replies
            $comments = Comment::where('post_id', $post->id)
                ->whereNull('parent_id')
                ->where('is_approved', true) // Changed from 'approved' to match migration
                ->with(['replies' => function($query) {
                    $query->where('is_approved', true)->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get related posts from same category
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
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Article not found');
        } catch (\Exception $e) {
            Log::error('Error showing post: ' . $e->getMessage(), ['slug' => $slug]);
            abort(500, 'Unable to load article');
        }
    }

    /**
     * Store a new comment
     */
    public function storeComment(Request $request, string $slug)
    {
        try {
            // Find the post
            $post = Post::where('slug', $slug)
                ->published()
                ->firstOrFail();

            // Validate the request
            $validated = $request->validate([
                'name'      => 'required|string|max:100',
                'email'     => 'nullable|email|max:255',
                'comment'   => 'required|string|min:5|max:1000',
                'parent_id' => 'nullable|exists:comments,id',
            ]);

            // Check for spam (simple check - can be enhanced)
            $spamKeywords = ['viagra', 'casino', 'porn', 'xxx', 'gambling'];
            $commentLower = strtolower($validated['comment']);
            foreach ($spamKeywords as $keyword) {
                if (strpos($commentLower, $keyword) !== false) {
                    return back()->with('error', 'Your comment contains inappropriate content.')->withInput();
                }
            }

            // Create the comment
            $comment = Comment::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'] ?? null,
                'content'    => $validated['comment'], // Changed from 'comment' to 'content' to match migration
                'post_id'    => $post->id,
                'parent_id'  => $validated['parent_id'] ?? null,
                'is_approved'=> false, // Changed from 'approved' to 'is_approved'
                'user_id'    => auth()->id() ?? null, // If user is logged in
                'ip_address' => $request->ip(), // Track IP for spam prevention
            ]);

            // Clear cache for this post's comments
            Cache::forget("post_{$slug}");
            
            // Optional: Send notification to admin
            // $this->notifyAdminOfNewComment($comment);

            return back()->with('success', 'Your comment has been submitted and is awaiting moderation. Thank you!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error storing comment: ' . $e->getMessage(), [
                'slug' => $slug,
                'data' => $request->except('comment')
            ]);
            return back()->with('error', 'Unable to post comment. Please try again later.')->withInput();
        }
    }

    /**
     * Increment post views without affecting cache
     */
    private function incrementPostViews(int $postId): void
    {
        try {
            // Use database update directly to avoid cache invalidation
            DB::table('posts')
                ->where('id', $postId)
                ->increment('view_count');
        } catch (\Exception $e) {
            Log::warning('Failed to increment view count: ' . $e->getMessage());
        }
    }

    /**
     * Calculate reading time in minutes
     */
    private function calculateReadingTime(?string $content): int
    {
        if (empty($content)) {
            return 1;
        }
        
        // Strip HTML tags and count words
        $plainText = strip_tags($content);
        $wordCount = str_word_count($plainText);
        
        // Average reading speed: 200-250 words per minute
        $minutes = max(1, ceil($wordCount / 200));
        
        return $minutes;
    }

    /**
     * Display comments for a post (AJAX endpoint)
     */
    public function getComments(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        
        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['replies' => function($query) {
                $query->where('is_approved', true)->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        if ($request->ajax()) {
            return response()->json($comments);
        }
        
        return back();
    }

    /**
     * Like/upvote a comment
     */
    public function likeComment(Request $request, int $commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            
            // Simple like increment (can be enhanced with user-based tracking)
            $comment->increment('likes');
            
            return response()->json(['success' => true, 'likes' => $comment->likes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to like comment'], 500);
        }
    }
}