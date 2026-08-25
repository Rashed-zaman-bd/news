<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryAdsRequest;
use App\Http\Requests\UpdateCategoryAdsRequest;
use App\Http\Resources\CategoryAdsResource;
use App\Models\CategoryPageAds;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class CategoryAdsController extends Controller
{
    /**
     * Public - Active category page advertisements
     *
     * GET /api/categorypageads
     * GET /api/categorypageads?placement=top&limit=3
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'placement' => [
                'nullable',
                'in:top,middle,middle-two,middle-three,sidebar,sidebar-two',
            ],
            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $query = CategoryPageAds::active();

        if ($request->filled('placement')) {
            $query->placement($request->string('placement')->toString());
        }

        $ads = $query
            ->orderBy('sort_order')
            ->limit($request->integer('limit', 10))
            ->get();

        return CategoryAdsResource::collection($ads);
    }

    /**
     * Admin - All category page advertisements
     *
     * GET /api/admin/categorypageads
     */
    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'placement' => [
                'nullable',
                'in:top,middle,middle-two,middle-three,sidebar,sidebar-two',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $query = CategoryPageAds::query();

        if ($request->filled('placement')) {
            $query->placement($request->string('placement')->toString());
        }

        if (
            $request->has('is_active') &&
            $request->input('is_active') !== ''
        ) {
            $query->where(
                'is_active',
                $request->boolean('is_active')
            );
        }

        $ads = $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit($request->integer('limit', 100))
            ->get();

        return CategoryAdsResource::collection($ads);
    }

    /**
     * Show a single advertisement.
     */
    public function show(CategoryPageAds $advertisement): CategoryAdsResource
    {
        return new CategoryAdsResource($advertisement);
    }

    /**
     * Create advertisement.
     */
    public function store(StoreCategoryAdsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('CategoryAds', 'public');
        }

        $advertisement = CategoryPageAds::create($data);

        return (new CategoryAdsResource($advertisement))
            ->additional([
                'message' => 'বিজ্ঞাপন সফলভাবে তৈরি হয়েছে।',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update advertisement.
     */
    public function update(
        UpdateCategoryAdsRequest $request,
        CategoryPageAds $advertisement
    ): JsonResponse {
        $data = $request->validated();

        if (
            array_key_exists('is_active', $data) ||
            $request->has('is_active')
        ) {
            $data['is_active'] = $request->boolean('is_active');
        }

        /*
         * Store the new image first.
         */
        if ($request->hasFile('image')) {
            $oldImage = $advertisement->image;

            $data['image'] = $request
                ->file('image')
                ->store('CategoryAds', 'public');

            /*
             * Delete old image after new image is stored.
             */
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $advertisement->update($data);

        return (new CategoryAdsResource($advertisement->fresh()))
            ->additional([
                'message' => 'বিজ্ঞাপন সফলভাবে আপডেট হয়েছে।',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Delete advertisement.
     */
    public function destroy(CategoryPageAds $advertisement): JsonResponse
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
     * Register advertisement click.
     *
     * POST /api/categorypageads/{advertisement}/click
     */
    public function click(CategoryPageAds $advertisement): JsonResponse
    {
        /*
         * Don't count clicks on inactive/expired ads.
         */
        if (
            !$advertisement->is_active ||
            (
                $advertisement->starts_at &&
                $advertisement->starts_at->isFuture()
            ) ||
            (
                $advertisement->ends_at &&
                $advertisement->ends_at->isPast()
            )
        ) {
            return response()->json([
                'message' => 'এই বিজ্ঞাপনটি বর্তমানে সক্রিয় নয়।',
            ], 404);
        }

        $advertisement->increment('clicks');

        return response()->json([
            'message' => 'Click recorded successfully.',
            'redirect' => $advertisement->link_url,
        ]);
    }
}