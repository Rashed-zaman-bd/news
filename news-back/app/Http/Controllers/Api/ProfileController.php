<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Show the currently authenticated user's own profile.
     */
    public function show(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource(Auth::user()),
        ]);
    }

    /**
     * PUT/PATCH /api/profile
     * Update the currently authenticated user's own profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        try {
            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => new UserResource($user->fresh()),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/profile/password
     * Change the currently authenticated user's own password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!Hash::check($request->validated('current_password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => $request->validated('password'), // hashed automatically via cast
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * DELETE /api/profile
     * Soft delete the currently authenticated user's own account.
     */
    public function destroy(): JsonResponse
    {
        $user = Auth::user();

        // Revoke all tokens so the deleted account can't keep making API calls
        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your account has been deleted.',
        ]);
    }
}