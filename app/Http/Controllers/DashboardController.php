<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Post counts
        $publishedCount = Post::where('status', 'published')->count();
        $draftCount = Post::where('status', 'draft')->count();
        
        // Pending comments (where is_approved = false)
        $pendingComments = Comment::where('is_approved', false)->count();
        
        // Total views
        $totalViews = Post::sum('view_count');
        
        // Recent posts (last 5)
        $recentPosts = Post::with(['category', 'author'])
            ->latest()
            ->take(5)
            ->get();
        
        // Latest pending comments (where is_approved = false)
        $latestPendingComments = Comment::where('is_approved', false)
            ->latest()
            ->take(5)
            ->get();
        
        // Top posts by views
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