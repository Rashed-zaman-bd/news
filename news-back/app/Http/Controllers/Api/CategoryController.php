<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
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

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            ...$request->validated(),
            'slug' => Str::slug($request->slug ?? $request->name),
        ]);

        return new CategoryResource($category);
    }
}
