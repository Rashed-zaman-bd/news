<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opinion extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'writer_name',
        'writer_designation',
        'writer_image',
        'text',
        'image',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'sort_order'   => 'integer',
    ];

    /**
     * Route by slug instead of numeric id, matching Article's pattern.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Public-facing published opinions only, most recent/curated first.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }
}
