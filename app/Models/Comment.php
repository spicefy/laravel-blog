<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'parent_id',
        'user_id',       // FIX: added — PostController sets this
        'name',
        'email',
        'comment',       // canonical column name (was conflicting with 'content' in PostController)
        'approved',      // canonical column name (was conflicting with 'is_approved' in PostController)
        'ip_address',    // FIX: added — PostController stores this
        'likes',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'likes'    => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    /** The post this comment belongs to. */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * The parent comment (null for top-level comments).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Approved direct replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->where('approved', true)   // FIX: was 'approved' — now consistent
                    ->latest();
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /** Only approved comments. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    /** Only top-level comments (no parent). */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Pending moderation. */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('approved', false);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /** Returns true when this comment is a reply to another. */
    public function isReply(): bool
    {
        return ! is_null($this->parent_id);
    }

    /** Approve this comment and save. */
    public function approve(): bool
    {
        return $this->update(['approved' => true]);
    }

    /** Increment like count with a single UPDATE — no model reload needed. */
    public function incrementLikes(): void
    {
        static::where('id', $this->id)->increment('likes');
    }
}
