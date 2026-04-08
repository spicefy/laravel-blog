<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /** All posts carrying this tag (any status). */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    /** Published posts carrying this tag, most recent first. */
    public function publishedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')
                    ->where('status', 'published')
                    ->where('published_at', '<=', now())
                    ->orderByDesc('published_at');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /** Tags that have at least one published post — useful for tag clouds. */
    public function scopeWithPublishedPosts(Builder $query): Builder
    {
        return $query->whereHas('posts', fn (Builder $q) =>
            $q->where('status', 'published')
              ->where('published_at', '<=', now())
        );
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Absolute URL for the tag archive page.
     */
    public function getUrlAttribute(): string
    {
        return route('tag.show', $this->slug);
    }
}