<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/Admin/DashboardController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-posts']);
    }

    public function index()
    {
        $publishedCount       = Post::where('status', 'published')->count();
        $draftCount           = Post::where('status', 'draft')->count();
        $pendingComments      = Comment::pending()->count();
        $totalViews           = Post::sum('view_count');

        $recentPosts = Post::with(['author', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $latestPendingComments = Comment::pending()
            ->with('post')
            ->latest()
            ->take(5)
            ->get();

        $topPosts = Post::published()
            ->with('category')
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'publishedCount', 'draftCount', 'pendingComments', 'totalViews',
            'recentPosts', 'latestPendingComments', 'topPosts'
        ));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/Admin/CommentController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-posts']);
    }

    public function index()
    {
        $filter = request('filter', 'pending');

        $comments = Comment::with(['post'])
            ->when($filter === 'pending',  fn ($q) => $q->pending())
            ->when($filter === 'approved', fn ($q) => $q->approved())
            ->latest()
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->approve();
        return back()->with('success', 'Comment approved.');
    }

    public function approveAll()
    {
        Comment::pending()->update(['approved' => true]);
        return back()->with('success', 'All pending comments approved.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/Admin/CategoryController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-posts']);
    }

    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'slug'             => 'nullable|string|max:100',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:60',
            'description'      => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $data['slug']  = Str::slug($data['slug'] ?? $data['name']);
        $data['color'] = $request->color_text ?? $data['color'] ?? '#2755c8';

        Category::create($data);
        return back()->with('success', "Category \"{$data['name']}\" created.");
    }

    public function edit(Category $category)
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'))->with('editing', $category);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'slug'             => 'nullable|string|max:100',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:60',
            'description'      => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $data['color'] = $request->color_text ?? $data['color'] ?? $category->color;

        $category->update($data);
        return redirect()->route('admin.categories.index')
                         ->with('success', "Category \"{$category->name}\" updated.");
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// app/Http/Controllers/Admin/TagController.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-posts']);
    }

    public function index()
    {
        $tags = Tag::withCount('posts')->orderByDesc('posts_count')->paginate(30);
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $data['slug'] = Str::slug($data['name']);
        Tag::firstOrCreate(['slug' => $data['slug']], $data);
        return back()->with('success', "Tag \"{$data['name']}\" created.");
    }

    public function destroy(Tag $tag)
    {
        $tag->delete(); // pivot rows cascade
        return back()->with('success', 'Tag deleted.');
    }
}