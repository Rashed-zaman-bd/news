<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\AdminCategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Log;

class AdminCategoryController extends Controller
{
    // Returns ALL parent categories (active + inactive) with their children, for the admin table
    public function index()
    {
        $categories = Category::parents()
            ->with('children')
            ->orderBy('order')
            ->get();

        return AdminCategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $category = Category::create([
            ...$data,
            'slug' => Str::slug($data['slug'] ?? $data['name']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ক্যাটাগরি তৈরি হয়েছে।',
            'data' => new AdminCategoryResource($category),
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        // Prevent a parent from being reassigned as a child of one of its own children (cycle guard)
        if (!empty($data['parent_id'])) {
            $childIds = $category->children()->pluck('id')->all();
            if (in_array((int) $data['parent_id'], $childIds, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'একটি ক্যাটাগরিকে তার নিজের সাব-ক্যাটাগরির অধীনে রাখা যাবে না।',
                ], 422);
            }
        }

        $category->update([
            ...$data,
            'slug' => Str::slug($data['slug'] ?? $data['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ক্যাটাগরি আপডেট হয়েছে।',
            'data' => new AdminCategoryResource($category->fresh()),
        ]);
    }

    public function destroy(Category $category): JsonResponse
{
    try {
        if ($category->children()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'সাব-ক্যাটাগরি থাকা অবস্থায় এই ক্যাটাগরি ডিলিট করা যাবে না।',
            ], 422);
        }

        if ($category->articles()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'এই ক্যাটাগরিতে আর্টিকেল থাকায় ডিলিট করা যাবে না।',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'ক্যাটাগরি ডিলিট হয়েছে।',
        ]);
    } catch (\Throwable $e) {
        Log::error('Category delete failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'ক্যাটাগরি ডিলিট করতে সমস্যা হয়েছে।',
        ], 500);
    }
}
}
