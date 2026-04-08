<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/CategoryController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Cache::remember("category_{$slug}_page_" . request('page', 1), 600, function () use ($slug) {
            return \App\Models\Post::published()
                ->forCategory($slug)
                ->with(['author', 'category'])
                ->latest()
                ->paginate(12);
        });

        return view('pages.category.show', compact('category', 'posts'));
    }
}
