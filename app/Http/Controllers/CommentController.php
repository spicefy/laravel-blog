<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // ── Store ──────────────────────────────────────────────────────────────

    /**
     * Validate and persist a new comment (or reply) for a post.
     *
     * Route: POST /news/{slug}/comments
     * Name:  post.comment
     */
    public function store(Request $request, string $slug): RedirectResponse
{
    $post = Post::where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

    $validated = $request->validate([
        'name'      => ['required', 'string', 'max:100'],
        'email'     => ['nullable', 'email', 'max:255'],
        'comment'   => ['required', 'string', 'max:2000'],
        'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
    ]);

    // Validate parent belongs to same post
    if (!empty($validated['parent_id'])) {
        $parentExists = Comment::where('id', $validated['parent_id'])
            ->where('post_id', $post->id)
            ->where('approved', true)
            ->exists();

        abort_if(!$parentExists, 422, 'Invalid parent comment.');
    }

    Comment::create([
        'post_id'   => $post->id,
        'parent_id' => $validated['parent_id'] ?? null,
        'name'      => $validated['name'],
        'email'     => $validated['email'] ?? null,
        'comment'   => $validated['comment'], // ✅ FIXED
        'approved'  => false,
        'likes'     => 0,
    ]);

    return redirect()
        ->to(route('post.show', $slug) . '#comments')
        ->with('success', 'Thanks! Your comment is awaiting moderation.');
}
    // ── Like ───────────────────────────────────────────────────────────────

    /**
     * Increment the like count on an approved comment.
     */
    public function like(Request $request, Comment $comment): \Illuminate\Http\JsonResponse
    {
        abort_if(!$comment->approved, 403);

        $liked = $request->session()->get('liked_comments', []);

        if (in_array($comment->id, $liked)) {
            return response()->json([
                'likes'   => $comment->likes,
                'message' => 'already_liked',
            ]);
        }

        $comment->increment('likes');

        $liked[] = $comment->id;
        $request->session()->put('liked_comments', $liked);

        return response()->json([
            'likes'   => $comment->likes,
            'message' => 'liked',
        ]);
    }

    // ── Destroy ────────────────────────────────────────────────────────────

    /**
     * Delete a comment (admin only).
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }

    // ── Approve ────────────────────────────────────────────────────────────

    /**
     * Approve a pending comment.
     */
    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['approved' => true]);

        return back()->with('success', 'Comment approved.');
    }
}