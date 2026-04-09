<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = Post::with(['category', 'author', 'tags'])
            ->latest()
            ->paginate(10);
        
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::all();
        $allTags = Tag::all();
        return view('admin.posts.create', compact('categories', 'allTags'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);
        
        // Handle slug
        if ($request->filled('slug')) {
            $validated['slug'] = Str::slug($request->slug);
        } else {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Post::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = $path;
        }
        
        $validated['user_id'] = auth()->id();
        $validated['view_count'] = 0;
        
        // Create the post
        $post = Post::create($validated);
        
        // Sync tags
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $allTags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'allTags'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);
        
        // Handle slug
        if ($request->filled('slug')) {
            $validated['slug'] = Str::slug($request->slug);
        } else if ($validated['title'] !== $post->title) {
            $validated['slug'] = Str::slug($validated['title']);
        } else {
            $validated['slug'] = $post->slug;
        }
        
        // Ensure slug is unique (excluding current post)
        if ($validated['slug'] !== $post->slug) {
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Post::where('slug', $validated['slug'])->where('id', '!=', $post->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Update the post
        $post->update($validated);
        
        // Sync tags (remove all if none selected)
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->sync([]);
        }
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        // Delete featured image if exists
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        
        // Detach all tags (many-to-many relationship)
        $post->tags()->detach();
        
        // Delete the post
        $post->delete();
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}