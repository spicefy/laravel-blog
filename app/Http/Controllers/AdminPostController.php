<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get post counts
        $publishedCount = Post::where('status', 'published')->count();
        $draftCount = Post::where('status', 'draft')->count();
        
        // Get pending comments count
        $pendingComments = Comment::where('status', 'pending')->count();
        
        // Get total views across all posts
        $totalViews = Post::sum('view_count');
        
        // Get recent posts (last 5)
        $recentPosts = Post::with(['category', 'author'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get latest pending comments (last 5)
        $latestPendingComments = Comment::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        
        // Get top posts by views (top 5)
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