<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/CategoryController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        // FIX: use firstOrFail() so a bad slug gives a clean 404
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $page = request('page', 1);

        $posts = Cache::remember("category_{$slug}_page_{$page}", 600, function () use ($slug) {
            // FIX: Post::scopeForCategory($slug) now exists — was calling an undefined scope before
            return Post::published()
                ->forCategory($slug)
                ->with(['author', 'category'])
                ->latest('published_at')   // FIX: be explicit — was just ->latest() (defaults to created_at)
                ->paginate(12);
        });

        return view('pages.category.show', compact('category', 'posts'));
    }
}
