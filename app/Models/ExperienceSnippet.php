<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceSnippet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'tags',
        'role_type',
        'industry',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'last_used_at' => 'datetime',
    ];

    /**
     * Increment usage count and update last used timestamp
     */
    public function markAsUsed(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Scope to filter by tags
     */
    public function scopeWithTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by role type
     */
    public function scopeForRole($query, string $roleType)
    {
        return $query->where('role_type', $roleType);
    }

    /**
     * Scope to get most used snippets
     */
    public function scopeMostUsed($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get recently used snippets
     */
    public function scopeRecentlyUsed($query, int $limit = 10)
    {
        return $query->whereNotNull('last_used_at')
            ->orderBy('last_used_at', 'desc')
            ->limit($limit);
    }

    /**
     * Search snippets by query string
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhereJsonContains('tags', $search);
        });
    }
}
