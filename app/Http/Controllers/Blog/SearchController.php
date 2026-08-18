<?php
// app/Http/Controllers/SearchController.php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q', ''));

        $posts = collect();

        if (strlen($query) >= 2) {
            $posts = Post::published()
                ->with(['author', 'category'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('excerpt', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%")
                      ->orWhere('meta_keywords', 'like', "%{$query}%");
                })
                ->latest('published_at')
                ->paginate(12)
                ->withQueryString();
        }

        return view('pages.search.index', compact('query', 'posts'));
    }
}