<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('homepage_data', 600, function () {
            $categories = Category::withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->with(['publishedPosts' => function ($query) {
                    $query->with('author')->limit(3);
                }])
                ->where('is_active', true)
                ->get();

            $recentPosts = Post::published()
                ->with(['author', 'category'])
                ->latest('published_at')
                ->take(8)
                ->get();

            return compact('categories', 'recentPosts');
        });

        return view('pages.news.index', $data);
    }
}