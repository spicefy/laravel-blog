<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with add/edit form integrated.
     */
    public function index(Request $request)
    {
        $categories = Category::withCount('posts')->paginate(10);
        
        // Check if we're editing a specific category
        $editing = null;
        if ($request->has('edit')) {
            $editing = Category::findOrFail($request->edit);
        }
        
        return view('admin.categories.index', compact('categories', 'editing'));
    }

    /**
     * Show the form for creating a new category.
     * Redirect to index with add form visible.
     */
    public function create()
    {
        return redirect()->route('admin.categories.index');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        // Redirect to frontend category page
        return redirect()->route('category.show', $category->slug);
    }

    /**
     * Show the form for editing the specified category.
     * Redirect to index with edit mode.
     */
    public function edit(Category $category)
    {
        return redirect()->route('admin.categories.index', ['edit' => $category->id]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        // Ensure slug is unique (excluding current category)
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Category::where('slug', $validated['slug'])->where('id', '!=', $category->id)->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Optionally update posts to remove category association
        $category->posts()->update(['category_id' => null]);
        
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}