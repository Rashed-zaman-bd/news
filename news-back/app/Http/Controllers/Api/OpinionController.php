<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpinionRequest;
use App\Http\Requests\UpdateOpinionRequest;
use App\Http\Resources\OpinionResource;
use App\Models\Opinion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OpinionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Opinion::query();

        $user = $request->user();
        $isStaff = $user && in_array($user->role, ['admin', 'editor']);

        if (!$isStaff) {
            // Public callers only ever see published opinions
            $query->published();
        } elseif ($request->filled('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $query->orderBy('sort_order')->latest('published_at');

        $opinions = $query->paginate($request->integer('per_page', 15));

        return OpinionResource::collection($opinions);
    }

    /**
     * Latest published opinions for homepage widgets (e.g. "মতামত" block).
     */
    public function latest(Request $request): AnonymousResourceCollection
    {
        $opinions = Opinion::query()
            ->published()
            ->orderBy('sort_order')
            ->latest('published_at')
            ->limit($request->integer('limit', 5))
            ->get();

        return OpinionResource::collection($opinions);
    }

    public function show(string $slug): OpinionResource
    {
        $opinion = Opinion::where('slug', $slug)
            ->published()
            ->firstOrFail();

        return new OpinionResource($opinion);
    }

    public function store(StoreOpinionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['slug'] = !empty($data['slug'])
            ? $data['slug']
            : $this->generateUniqueSlug($data['title']);

        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('writer_image')) {
            $data['writer_image'] = $request->file('writer_image')->store('opinions/writers', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('opinions', 'public');
        }

        $opinion = Opinion::create($data);

        return (new OpinionResource($opinion))
            ->additional(['message' => 'মতামত সফলভাবে তৈরি হয়েছে।'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateOpinionRequest $request, Opinion $opinion): OpinionResource
    {
        $data = $request->validated();

        if (array_key_exists('title', $data) && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $opinion->id);
        }

        if (($data['is_published'] ?? $opinion->is_published)
            && !$opinion->published_at
            && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('writer_image')) {
            if ($opinion->writer_image) {
                Storage::disk('public')->delete($opinion->writer_image);
            }
            $data['writer_image'] = $request->file('writer_image')->store('opinions/writers', 'public');
        }

        if ($request->hasFile('image')) {
            if ($opinion->image) {
                Storage::disk('public')->delete($opinion->image);
            }
            $data['image'] = $request->file('image')->store('opinions', 'public');
        }

        $opinion->update($data);

        return (new OpinionResource($opinion->fresh()))
            ->additional(['message' => 'মতামত সফলভাবে আপডেট হয়েছে।']);
    }

    public function destroy(Opinion $opinion): JsonResponse
    {
        if ($opinion->writer_image) {
            Storage::disk('public')->delete($opinion->writer_image);
        }
        if ($opinion->image) {
            Storage::disk('public')->delete($opinion->image);
        }

        $opinion->delete();

        return response()->json([
            'message' => 'মতামত সফলভাবে মুছে ফেলা হয়েছে।',
        ], 200);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title, '-', null);

        if (empty($base)) {
            $base = 'opinion-' . Str::random(6);
        }

        $slug = $base;
        $counter = 1;

        while (Opinion::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}