<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('homepage_data', 600, function () {
            $categories = Category::query()
                ->where('is_active', true)
                ->withCount([
                    'posts' => function ($query) {
                        $query->published();
                    },
                ])
                ->with([
                    'publishedPosts' => function ($query) {
                        $query->with('author')
                            ->latest('published_at')
                            ->limit(3);
                    },
                ])
                ->get();

            $recentPosts = Post::published()
                ->with(['author', 'category'])
                ->latest('published_at')
                ->limit(8)
                ->get();

            return [
                'categories' => $categories,
                'recentPosts' => $recentPosts,
            ];
        });

        return view('pages.news.index', $data);
    }
}