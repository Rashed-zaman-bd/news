<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertisementRequest;
use App\Http\Requests\UpdateAdvertisementRequest;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    // GET /api/advertisements — PUBLIC, always active only, no exceptions
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Advertisement::active(); // hard-coded — ignores who's logged in

        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        $query->orderBy('sort_order')->inRandomOrder();

        $ads = $query->limit($request->integer('limit', 10))->get();

        return AdvertisementResource::collection($ads);
    }

    // GET /api/admin/advertisements — STAFF ONLY, sees everything + status filter
    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $query = Advertisement::query();

        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $ads = $query->orderBy('sort_order')
            ->limit($request->integer('limit', 100))
            ->get();

        return AdvertisementResource::collection($ads);
    }

    // GET /api/advertisements/{advertisement}
    public function show(Advertisement $advertisement): AdvertisementResource
    {
        return new AdvertisementResource($advertisement);
    }

    // POST /api/advertisements
    public function store(StoreAdvertisementRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $advertisement = Advertisement::create($data);

        return (new AdvertisementResource($advertisement))
            ->additional(['message' => 'বিজ্ঞাপন সফলভাবে তৈরি হয়েছে।'])
            ->response()
            ->setStatusCode(201);
    }

    // PUT /api/advertisements/{advertisement}  (with _method: PUT spoofing for multipart)
    public function update(UpdateAdvertisementRequest $request, Advertisement $advertisement): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('is_active', $data) || $request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image')) {
            if ($advertisement->image) {
                Storage::disk('public')->delete($advertisement->image);
            }
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $advertisement->update($data);

        return (new AdvertisementResource($advertisement->fresh()))
            ->additional(['message' => 'বিজ্ঞাপন সফলভাবে আপডেট হয়েছে।'])
            ->response()
            ->setStatusCode(200);
    }

    // DELETE /api/advertisements/{advertisement}
    public function destroy(Advertisement $advertisement): JsonResponse
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
    public function click(Advertisement $advertisement): JsonResponse
    {
        $advertisement->increment('clicks');

        return response()->json(['redirect' => $advertisement->link_url]);
    }
}