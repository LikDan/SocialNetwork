<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ProfileUpdateRequest;
use App\Http\Resources\Api\v1\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function addPicture(Request $request)
    {
        $user = $request->user();

        $file = Storage::putFile('avatars', $request->avatar);
        $user->profile()->update(["picture_path" => $file]);

        return Storage::url($file);
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $profile = $request->validated();
        $user->profile()->update($profile);

        return ProfileResource::make($user->profile);
    }
}
