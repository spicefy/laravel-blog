<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/TagController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    public function show(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = \App\Models\Post::published()
            ->forTag($slug)
            ->with(['author', 'category'])
            ->latest()
            ->paginate(12);

        return view('pages.tag.show', compact('tag', 'posts'));
    }
}
