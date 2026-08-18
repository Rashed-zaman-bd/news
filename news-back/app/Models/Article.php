<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'excerpt',
        'content',
        'featured_image',
        'category_id',
        'sub_category_id',
        'user_id',
        'editor_id',
        'status',
        'is_featured',
        'is_breaking',
        'views',
        'published_at',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'is_breaking'  => 'boolean',
        'views'        => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Route model binding key.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // --- Relationships ---

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
{
    return $this->belongsTo(Category::class, 'sub_category_id');
}

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // --- Scopes ---

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }
}