<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    /**
     * Handle saving event
     */
    public function saving(Post $post): void
    {
        // Auto compute reading time
        $post->reading_time = Post::computeReadingTime($post->content ?? '');

        // Auto-set published_at when status changes to published
        if (
            $post->isDirty('status') &&
            $post->status === 'published' &&
            !$post->published_at
        ) {
            $post->published_at = now();
        }
    }

    /**
     * After save → clear caches
     */
    public function saved(Post $post): void
    {
        $this->bustCaches($post);
    }

    /**
     * After delete → clear caches
     */
    public function deleted(Post $post): void
    {
        $this->bustCaches($post);
    }

    /**
     * Cache cleanup
     */
    private function bustCaches(Post $post): void
    {
        Cache::forget("post_{$post->slug}");
        Cache::forget("related_{$post->id}");
        Cache::forget('homepage_data');
        Cache::forget('sitemap_xml');

        // Category cache cleanup (safe null check)
        $slug = $post->category?->slug;

        if ($slug) {
            for ($page = 1; $page <= 20; $page++) {
                Cache::forget("category_{$slug}_page_{$page}");
            }
        }
    }
}