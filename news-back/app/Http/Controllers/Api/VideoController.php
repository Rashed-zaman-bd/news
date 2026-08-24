<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(10);
        return VideoResource::collection($videos);
    }

    // Store new video
    public function store(StoreVideoRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $video = Video::create($validated);

        return new VideoResource($video);
    }

    // Show single video
    public function show(Video $video)
    {
        return new VideoResource($video);
    }

    // Update existing video
    public function update(UpdateVideoRequest $request, Video $video)
    {
        $validated = $request->validated();

        // Handle image update and delete old image
        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $video->update($validated);

        return new VideoResource($video);
    }

    // Delete video
    public function destroy(Video $video)
    {
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully.']);
    }
    
}
