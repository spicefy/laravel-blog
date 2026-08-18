<?php
// app/Http/Controllers/AuthorController.php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class AuthorController extends Controller
{
    public function show(int $id)
    {
        $author = User::findOrFail($id);

        $posts = Post::published()
            ->where('user_id', $author->id)
            ->with('category')
            ->latest('published_at')
            ->paginate(12);

        return view('pages.author.show', compact('author', 'posts'));
    }
}