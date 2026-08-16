<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Upload avatar if provided
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Apply business defaults
        $data['role'] = $data['role'] ?? User::ROLE_READER;
        $data['status'] = User::STATUS_ACTIVE;

        // NOTE: Do NOT use Hash::make() here because the User model 
        // handles password hashing automatically via protected $casts.
        $user = User::create($data);

        // Generate Sanctum access token
        $deviceName = $request->input('device_name', 'auth_token');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'রেজিস্ট্রেশন সফল হয়েছে!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 201);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        // Verify credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['আপনার প্রদানকৃত ফোন নম্বর বা পাসওয়ার্ড ভুল।'],
            ]);
        }

        // Account status checks
        if ($user->isSuspended()) {
            return response()->json([
                'message' => 'আপনার অ্যাকাউন্টটি স্থগিত করা হয়েছে। অনুগ্রহ করে সাপোর্টে যোগাযোগ করুন।',
            ], 403);
        }

        if ($user->isInactive()) {
            return response()->json([
                'message' => 'আপনার অ্যাকাউন্টটি বর্তমানে নিষ্ক্রিয়।',
            ], 403);
        }

        // Issue token
        $deviceName = $request->input('device_name', 'auth_token');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'লগইন সফল হয়েছে।',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }


    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }


    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        
        // Get validated input
        $data = $request->validated();

        // 1. Remove password if empty/null so it isn't overwritten or double-hashed
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        // 2. Handle avatar upload and remove old file if present
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // 3. Perform update with fillable attributes
        $user->fill($data);
        $user->save();

        return response()->json([
            'message' => 'প্রোফাইল সফলভাবে আপডেট করা হয়েছে।',
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Log out current session (Revoke current token).
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Log out from all devices (Revoke all tokens).
     * POST /api/logout-all
     */
    public function logoutAllDevices(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully.',
        ]);
    }
}