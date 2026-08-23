<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CategoryResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // For public navbar — parents with their children eager loaded
    public function index()
    {
        $categories = Category::parents()
            ->where('is_active', true)
            ->with('activeChildren')
            ->orderBy('order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->with('activeChildren:id,name,slug,icon,parent_id,is_active')
            ->firstOrFail();

        return new CategoryResource($category);
    }

    public function articles(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->with('activeChildren:id,name,slug,icon,parent_id,is_active')
            ->firstOrFail();

        $categoryIds = $category->activeChildren->pluck('id')->push($category->id);

        $articles = Article::query()
            ->whereIn('category_id', $categoryIds)
            ->where('status', 'published')
            ->when($request->filled('sub_category'), function ($query) use ($request) {
                $query->whereHas('subCategory', function ($q) use ($request) {
                    $q->where('slug', $request->string('sub_category'));
                });
            })
            ->with(['category:id,name,slug', 'subCategory:id,name,slug', 'author:id,name'])
            ->latest('published_at')
            ->paginate($request->integer('per_page', 12));

        return ArticleResource::collection($articles)->additional([
            'category' => new CategoryResource($category),
        ]);
    }

    public function popular(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->with('activeChildren:id,parent_id') // fine here — children not serialized in this response
            ->firstOrFail();

        $categoryIds = $category->activeChildren->pluck('id')->push($category->id);

        $articles = Article::query()
            ->whereIn('category_id', $categoryIds)
            ->where('status', 'published')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        return ArticleResource::collection($articles);
    }
}