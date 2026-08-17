<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Category.php
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'parent_id', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Parent category (e.g. Football -> Sports)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Direct children (e.g. Sports -> [Football, Cricket])
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    // Only active children — useful for public nav menus
    public function activeChildren()
    {
        return $this->children()->where('is_active', true);
    }

    // Articles under this category
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    // Scope: only top-level (parent) categories
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Helper: is this a subcategory?
    public function isSubcategory(): bool
    {
        return $this->parent_id !== null;
    }
}
