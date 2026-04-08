<?php
// app/Http/Controllers/Admin/PostController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-posts']);
    }

    public function index()
    {
        $posts = Post::with(['author', 'category'])
            ->latest()
            ->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.form', [
            'post'       => new Post(),
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
            'action'     => route('admin.posts.store'),
            'method'     => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['author_id'] = auth()->id();
        $data['slug']      = $this->uniqueSlug($request->slug ?: $request->title);

        $post = Post::create($data);
        $post->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('admin.posts.edit', $post)
                         ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', [
            'post'       => $post->load('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
            'action'     => route('admin.posts.update', $post),
            'method'     => 'PUT',
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request);

        // Only regenerate slug if the title changed and no custom slug given
        if (! $request->filled('slug')) {
            $data['slug'] = $this->uniqueSlug($request->title, $post->id);
        }

        $post->update($data);
        $post->tags()->sync($request->input('tag_ids', []));

        return back()->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')
                         ->with('success', 'Post deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|string|max:500',
            'category_id'      => 'required|exists:categories,id',
            'status'           => 'required|in:draft,published',
            'published_at'     => 'nullable|date',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords'    => 'nullable|string|max:255',
        ]);
    }

    private function uniqueSlug(string $source, ?int $exceptId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $i    = 1;

        while (Post::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
