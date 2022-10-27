<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\UsersAuthRequest;
use App\Http\Requests\Api\v1\UsersSignupRequest;
use App\Http\Resources\Api\v1\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function signup(UsersSignupRequest $request): UserResource
    {
        $user = User::create($request->validated());
        return UserResource::make($user);
    }

    /**
     * @throws AuthorizationException
     */
    public function login(UsersAuthRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!Hash::check($request->password, $user->password)) {
            throw new AuthorizationException();
        }

        $token = $user->createToken("auth")->plainTextToken;
        return response()->json([
            "token" => $token,
            "user" => UserResource::make($user),
        ]);
    }

    public function getUser(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }
}
