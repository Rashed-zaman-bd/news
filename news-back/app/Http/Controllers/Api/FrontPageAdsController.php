<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFrontPageAdsRequest;
use App\Http\Requests\UpdateFrontPageAdsRequest;
use App\Http\Resources\FrontPageAdsResource;
use App\Models\FrontPageAdes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class FrontPageAdsController extends Controller
{
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

        $query = FrontPageAdes::active();

        if ($request->filled('placement')) {
            $query->placement($request->string('placement')->toString());
        }

        $ads = $query
            ->orderBy('sort_order')
            ->limit($request->integer('limit', 10))
            ->get();

        return FrontPageAdsResource::collection($ads);
    }


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

        $query = FrontPageAdes::query();

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

        return FrontPageAdsResource::collection($ads);
    }


     public function show(FrontPageAdes $advertisement): FrontPageAdsResource
    {
        return new FrontPageAdsResource($advertisement);
    }


    public function store(StoreFrontPageAdsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('FrontAds', 'public');
        }

        $advertisement = FrontPageAdes::create($data);

        return (new FrontPageAdsResource($advertisement))
            ->additional([
                'message' => 'বিজ্ঞাপন সফলভাবে তৈরি হয়েছে।',
            ])
            ->response()
            ->setStatusCode(201);
    }


    public function update(
        UpdateFrontPageAdsRequest $request,
        FrontPageAdes $advertisement
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
                ->store('FrontAds', 'public');

            /*
             * Delete old image after new image is stored.
             */
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $advertisement->update($data);

        return (new FrontPageAdsResource($advertisement->fresh()))
            ->additional([
                'message' => 'বিজ্ঞাপন সফলভাবে আপডেট হয়েছে।',
            ])
            ->response()
            ->setStatusCode(200);
    }


    public function destroy(FrontPageAdes $advertisement): JsonResponse
    {
        if ($advertisement->image) {
            Storage::disk('public')->delete($advertisement->image);
        }

        $advertisement->delete();

        return response()->json([
            'message' => 'বিজ্ঞাপন সফলভাবে মুছে ফেলা হয়েছে।',
        ]);
    }


    public function click(FrontPageAdes $advertisement): JsonResponse
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
