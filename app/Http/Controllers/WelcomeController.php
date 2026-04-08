<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/HomeController.php TEST
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function index()
    {
        // Cache the homepage data for 10 minutes (high-traffic page)
        $data = Cache::remember('homepage_data', 600, function () {
            $categories = Category::withCount(['posts' => fn ($q) => $q->published()])
                ->with(['publishedPosts' => fn ($q) => $q->with('author')->take(3)])
                ->get();

            $recentPosts = Post::published()
                ->with(['author', 'category'])
                ->latest()
                ->take(8)
                ->get();

            return compact('categories', 'recentPosts');
        });

        return view('pages.news.index', $data);
    }
}