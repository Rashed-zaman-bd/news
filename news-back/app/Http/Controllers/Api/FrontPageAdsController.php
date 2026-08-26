<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFrontPageAdsRequest;
use App\Http\Requests\UpdateFrontPageAdsRequest;
use App\Http\Resources\FrontPageAdsResource;
use App\Models\FrontPageAds; // Fixed model name standard
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FrontPageAdsController extends Controller
{
    /**
     * Public ads display
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'placement' => ['nullable', Rule::in(StoreFrontPageAdsRequest::placements())],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = FrontPageAds::active();

        if ($request->filled('placement')) {
            $query->where('placement', $request->input('placement'));
        }

        $ads = $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit($request->integer('limit', 10))
            ->get();

        return FrontPageAdsResource::collection($ads);
    }

    /**
     * Admin ads index
     */
    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'placement' => ['nullable', Rule::in(StoreFrontPageAdsRequest::placements())],
            'is_active' => ['nullable', 'boolean'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = FrontPageAds::query();

        if ($request->filled('placement')) {
            $query->where('placement', $request->input('placement'));
        }

        if ($request->has('is_active') && $request->input('is_active') !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $ads = $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100))
            ->get();

        return FrontPageAdsResource::collection($ads);
    }

    /**
     * Show single advertisement
     */
    public function show(FrontPageAds $advertisement): FrontPageAdsResource
    {
        return new FrontPageAdsResource($advertisement);
    }

    /**
     * Store advertisement
     */
    public function store(StoreFrontPageAdsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('FrontAds', 'public');
        }

        $advertisement = FrontPageAds::create($data);

        return response()->json([
            'message' => 'বিজ্ঞাপন সফলভাবে তৈরি হয়েছে।',
            'data'    => new FrontPageAdsResource($advertisement),
        ], 201);
    }

    /**
     * Update advertisement
     */
    public function update(
        UpdateFrontPageAdsRequest $request,
        FrontPageAds $advertisement
    ): JsonResponse {
        $data = $request->validated();

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image')) {
            if ($advertisement->image) {
                Storage::disk('public')->delete($advertisement->image);
            }
            $data['image'] = $request->file('image')->store('FrontAds', 'public');
        }

        $advertisement->update($data);

        return response()->json([
            'message' => 'বিজ্ঞাপন সফলভাবে আপডেট হয়েছে।',
            'data'    => new FrontPageAdsResource($advertisement->fresh()),
        ], 200);
    }

    /**
     * Delete advertisement
     */
    public function destroy(FrontPageAds $advertisement): JsonResponse
    {
        if ($advertisement->image) {
            Storage::disk('public')->delete($advertisement->image);
        }

        $advertisement->delete();

        return response()->json([
            'message' => 'বিজ্ঞাপন সফলভাবে মুছে ফেলা হয়েছে।',
        ]);
    }

    /**
     * Track click
     */
    public function click(FrontPageAds $advertisement): JsonResponse
    {
        if (!$advertisement->is_active) {
            return response()->json([
                'message' => 'এই বিজ্ঞাপনটি বর্তমানে সক্রিয় নয়।',
            ], 404);
        }

        if ($advertisement->starts_at && $advertisement->starts_at->isFuture()) {
            return response()->json([
                'message' => 'এই বিজ্ঞাপনটি এখনও শুরু হয়নি।',
            ], 404);
        }

        if ($advertisement->ends_at && $advertisement->ends_at->isPast()) {
            return response()->json([
                'message' => 'এই বিজ্ঞাপনের সময় শেষ হয়েছে।',
            ], 404);
        }

        $advertisement->increment('clicks');

        return response()->json([
            'message'  => 'Click recorded successfully.',
            'redirect' => $advertisement->link_url,
        ]);
    }
}