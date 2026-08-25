<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryAdsRequest;
use App\Http\Requests\UpdateCategoryAdsRequest;
use App\Http\Resources\CategoryAdsResource;
use App\Models\CategoryPageAds;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class CategoryAdsController extends Controller
{
     public function index(Request $request): AnonymousResourceCollection
    {
        $query = CategoryPageAds::active();

        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        $query->orderBy('sort_order')->inRandomOrder();

        $ads = $query->limit($request->integer('limit', 10))->get();

        return CategoryAdsResource::collection($ads);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $query = CategoryPageAds::query();

        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $ads = $query->orderBy('sort_order')
            ->limit($request->integer('limit', 100))
            ->get();

        return CategoryAdsResource::collection($ads);
    }


      public function show(CategoryPageAds $advertisement): CategoryAdsResource
    {
        return new CategoryAdsResource($advertisement);
    }


      public function store(StoreCategoryAdsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('CategoryAds', 'public');
        }

        $advertisement = CategoryPageAds::create($data);

        return (new CategoryAdsResource($advertisement))
            ->additional(['message' => 'বিজ্ঞাপন সফলভাবে তৈরি হয়েছে।'])
            ->response()
            ->setStatusCode(201);
    }


     public function update(UpdateCategoryAdsRequest $request, CategoryPageAds $advertisement): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('is_active', $data) || $request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image')) {
            if ($advertisement->image) {
                Storage::disk('public')->delete($advertisement->image);
            }
            $data['image'] = $request->file('image')->store('CategoryAds', 'public');
        }

        $advertisement->update($data);

        return (new CategoryAdsResource($advertisement->fresh()))
            ->additional(['message' => 'বিজ্ঞাপন সফলভাবে আপডেট হয়েছে।'])
            ->response()
            ->setStatusCode(200);
    }


    public function destroy(CategoryPageAds $advertisement): JsonResponse
    {
        if ($advertisement->image) {
            Storage::disk('public')->delete($advertisement->image);
        }

        $advertisement->delete();

        return response()->json([
            'message' => 'বিজ্ঞাপন সফলভাবে মুছে ফেলা হয়েছে।',
        ], 200);
    }

    // POST /api/advertisements/{advertisement}/click
    public function click(CategoryPageAds $advertisement): JsonResponse
    {
        $advertisement->increment('clicks');

        return response()->json(['redirect' => $advertisement->link_url]);
    }
    
}
