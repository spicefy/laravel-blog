<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
    'post_id',
    'parent_id',
    'name',
    'email',
    'comment', // FIXED
    'approved',
    'likes',
];

    protected $casts = [
        'approved' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Direct parent comment (null for top-level comments).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Immediate child replies, with their own replies eager-loaded recursively.
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->where('approved', true)
                    ->with('replies')   // recursive eager-load
                    ->oldest();
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('approved', false);
    }

    /**
     * Only top-level comments (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}