<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['author', 'category', 'tags'])
            ->firstOrFail();

        // Reading time
        if (! $post->reading_time) {
            $post->reading_time = $this->calculateReadingTime($post->content);
            $post->saveQuietly();
        }

        // Views
        $this->incrementPostViews($post->id);

        // Comments
        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['replies' => function ($query) {
                $query->where('is_approved', true)
                    ->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Related posts
        $relatedPosts = Cache::remember("related_{$post->id}", 3600, function () use ($post) {
            return Post::published()
                ->where('id', '!=', $post->id)
                ->where('category_id', $post->category_id)
                ->with(['author', 'category'])
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        // 🔥 Trending posts (FIXED)
        $trendingPosts = Cache::remember('trending_posts', 600, function () {
            return Post::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->withCount('comments')
                ->orderByRaw('(view_count * 2) + (comments_count * 3) DESC')
                ->limit(5)
                ->get();
        });

        return view('pages.news.article', compact(
            'post',
            'comments',
            'relatedPosts',
            'trendingPosts'
        ));
    }

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

        // Spam filter
        foreach (['viagra', 'casino', 'porn', 'xxx', 'gambling'] as $keyword) {
            if (stripos($validated['comment'], $keyword) !== false) {
                return back()
                    ->with('error', 'Your comment contains inappropriate content.')
                    ->withInput();
            }
        }

        Comment::create([
            'post_id'     => $post->id,
            'parent_id'   => $validated['parent_id'] ?? null,
            'user_id'     => auth()->id(),
            'name'        => $validated['name'],
            'email'       => $validated['email'] ?? null,
            'comment'     => $validated['comment'],
            'is_approved' => false,
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Comment submitted for moderation.');
    }

    private function incrementPostViews(int $postId): void
    {
        DB::table('posts')
            ->where('id', $postId)
            ->increment('view_count', 1);
    }

    private function calculateReadingTime(?string $content): int
    {
        if (empty($content)) return 1;

        return max(1, (int) ceil(str_word_count(strip_tags($content)) / 200));
    }
}