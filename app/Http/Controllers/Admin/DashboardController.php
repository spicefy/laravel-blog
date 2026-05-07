<?php
//contrrollers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $publishedCount = Post::where('status', 'published')->count();
        $draftCount = Post::where('status', 'draft')->count();

        $pendingComments = Comment::where('is_approved', false)->count();

        $totalViews = Post::sum('view_count');

        $recentPosts = Post::with(['category', 'author'])
            ->latest()
            ->take(5)
            ->get();

        $latestPendingComments = Comment::where('is_approved', false)
            ->latest()
            ->take(5)
            ->get();

        $topPosts = Post::with('category')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'publishedCount',
            'draftCount',
            'pendingComments',
            'totalViews',
            'recentPosts',
            'latestPendingComments',
            'topPosts'
        ));
    }
}