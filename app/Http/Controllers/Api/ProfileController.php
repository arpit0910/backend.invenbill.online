<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return sendResponse([
            'user' => new UserResource($request->user()),
        ], 'Profile retrieved successfully', 200);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->name = $data['name'];

        if (array_key_exists('country_code', $data)) {
            $user->country_code = $data['country_code'];
        }

        if (array_key_exists('mobile', $data)) {
            $user->mobile = $data['mobile'];
        }

        if (array_key_exists('gender', $data)) {
            $user->gender = $data['gender'];
        }

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return sendResponse([
            'user' => new UserResource($user),
        ], 'Profile updated successfully', 200);
    }
}
