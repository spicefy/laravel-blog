<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Display a listing of comments.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['post', 'user']);
        
        // Filter by approval status
        if ($request->filter === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->filter === 'approved') {
            $query->where('is_approved', true);
        }
        
        $comments = $query->latest()->paginate(20);
        
        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment)
    {
        try {
            $comment->update(['is_approved' => true]);
            
            return redirect()
                ->back()
                ->with('success', 'Comment approved successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to approve comment: ' . $e->getMessage());
        }
    }

    /**
     * Disapprove (unapprove) a comment.
     */
    public function disapprove(Comment $comment)
    {
        try {
            $comment->update(['is_approved' => false]);
            
            return redirect()
                ->back()
                ->with('success', 'Comment unapproved successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to unapprove comment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        try {
            $comment->delete();
            
            return redirect()
                ->route('admin.comments.index')
                ->with('success', 'Comment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete comment: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk approve multiple comments.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id'
        ]);
        
        try {
            Comment::whereIn('id', $request->comment_ids)
                ->update(['is_approved' => true]);
            
            return redirect()
                ->back()
                ->with('success', count($request->comment_ids) . ' comments approved successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to approve comments: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk delete multiple comments.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id'
        ]);
        
        try {
            Comment::whereIn('id', $request->comment_ids)->delete();
            
            return redirect()
                ->back()
                ->with('success', count($request->comment_ids) . ' comments deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete comments: ' . $e->getMessage());
        }
    }
}