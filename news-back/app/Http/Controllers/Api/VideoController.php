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


    public function show(Video $video)
    {
        return new VideoResource($video);
    }

    // Store new video
   public function store(StoreVideoRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($validated['video_type'] === 'upload' && $request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('videos', 'public');
        }

        $video = Video::create($validated);

        return new VideoResource($video);
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if (($validated['video_type'] ?? $video->video_type) === 'upload' && $request->hasFile('video')) {
            if ($video->video) {
                Storage::disk('public')->delete($video->video);
            }
            $validated['video'] = $request->file('video')->store('videos', 'public');
        }

        $video->update($validated);

        return new VideoResource($video);
    }

    public function destroy(Video $video)
    {
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        if ($video->video) {
            Storage::disk('public')->delete($video->video);
        }

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully.']);
    }
    
}
