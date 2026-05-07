<?php

// app/Http/Controllers/Api/PostApiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostApiController extends Controller
{
    // GET /api/v1/news
    public function index()
    {
        $posts = Post::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => $posts,
        ]);
    }

    // GET /api/v1/news/{slug}
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['author', 'category', 'tags'])
            ->firstOrFail(); // automatically returns 404 JSON on missing model (see Handler)

        // Increment view count
        $post->incrementViews();

        // Related posts cached per post
        $relatedPosts = Cache::remember("related_{$post->id}", 3600, function () use ($post) {
            return Post::published()
                ->where('id', '!=', $post->id)
                ->where('category_id', $post->category_id)
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        return response()->json([
            'status'  => 'success',
            'post'    => $post,
            'related' => $relatedPosts,
        ]);
    }
}