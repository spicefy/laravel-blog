<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];
    
    protected static function booted()
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
            
            // Ensure slug is unique
            $originalSlug = $tag->slug;
            $count = 1;
            while (static::where('slug', $tag->slug)->exists()) {
                $tag->slug = $originalSlug . '-' . $count++;
            }
        });
        
        static::updating(function ($tag) {
            if ($tag->isDirty('name')) {
                $tag->slug = Str::slug($tag->name);
                
                // Ensure slug is unique (excluding current tag)
                $originalSlug = $tag->slug;
                $count = 1;
                while (static::where('slug', $tag->slug)->where('id', '!=', $tag->id)->exists()) {
                    $tag->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }
    
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
    
    /**
     * Get the post count for this tag
     */
    public function getPostCountAttribute(): int
    {
        return $this->posts()->count();
    }
    
    /**
     * Get popular tags with post count
     */
    public static function getPopularTags($limit = 10)
    {
        return static::withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Scope a query to only include tags with posts
     */
    public function scopeWithPosts($query)
    {
        return $query->has('posts');
    }
    
    /**
     * Scope a query to search tags by name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('slug', 'like', "%{$search}%");
    }
}