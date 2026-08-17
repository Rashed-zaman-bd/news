<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogoRequest;
use App\Http\Resources\LogoResource;
use App\Models\Logo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    /**
     * Get logo.
     */
    public function index()
    {
        $logo = Logo::latest()->first();

        if (!$logo) {
            return response()->json([
                'message' => 'Logo not found.',
            ], 404);
        }

        return new LogoResource($logo);
    }

    /**
     * Store logo.
     */
    public function store(LogoRequest $request): JsonResponse
    {
        // If only one logo is allowed
        $logo = Logo::first();

        if ($logo) {
            return response()->json([
                'message' => 'Logo already exists. Please update the existing logo.',
            ], 422);
        }

        $data = [
            'title' => $request->title,
        ];

        if ($request->hasFile('text_logo')) {
            $data['text_logo'] = $request
                ->file('text_logo')
                ->store('logos', 'public');
        }

        if ($request->hasFile('round_logo')) {
            $data['round_logo'] = $request
                ->file('round_logo')
                ->store('logos', 'public');
        }

        $logo = Logo::create($data);

        return response()->json([
            'message' => 'Logo created successfully.',
            'data' => new LogoResource($logo),
        ], 201);
    }

    /**
     * Update logo.
     */
    public function update(LogoRequest $request, Logo $logo): JsonResponse
    {
        $data = [
            'title' => $request->title,
        ];

        // Replace text logo
        if ($request->hasFile('text_logo')) {

            if ($logo->text_logo) {
                Storage::disk('public')->delete($logo->text_logo);
            }

            $data['text_logo'] = $request
                ->file('text_logo')
                ->store('logos', 'public');
        }

        // Replace round logo
        if ($request->hasFile('round_logo')) {

            if ($logo->round_logo) {
                Storage::disk('public')->delete($logo->round_logo);
            }

            $data['round_logo'] = $request
                ->file('round_logo')
                ->store('logos', 'public');
        }

        $logo->update($data);

        return response()->json([
            'message' => 'Logo updated successfully.',
            'data' => new LogoResource($logo->fresh()),
        ]);
    }

    /**
     * Delete logo.
     */
    public function destroy(Logo $logo): JsonResponse
    {
        if ($logo->text_logo) {
            Storage::disk('public')->delete($logo->text_logo);
        }

        if ($logo->round_logo) {
            Storage::disk('public')->delete($logo->round_logo);
        }

        $logo->delete();

        return response()->json([
            'message' => 'Logo deleted successfully.',
        ]);
    }
}