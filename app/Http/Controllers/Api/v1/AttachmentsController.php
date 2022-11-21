<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\AttachmentRequest;
use App\Http\Resources\Api\v1\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\JsonResponse;

class AttachmentsController extends Controller
{
    public function store(AttachmentRequest $request, string $profileId): JsonResponse|AttachmentResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);

        $image = $request->file("file");

        $img = Image::make($image->path());
        $profiles = $img->getCore()->getImageProfiles("icc", true);

        $img->getCore()->stripImage();

        if (!empty($profiles))
            $img->getCore()->profileImage("icc", $profiles['icc']);

        $random_hex = bin2hex(random_bytes(18));
        $path = "attachments/" . $random_hex . '.' . $image->getClientOriginalExtension();
        Storage::put($path, $img->stream());

        $attachment = [
            "path" => $path,
            "display_name" => $image->getClientOriginalName(),
            "type" => $image->getClientMimeType(),
            "profile_id" => $profile->id
        ];

        $attachment = Attachment::create($attachment);
        return AttachmentResource::make($attachment);
    }

    public function destroy(Request $request, string $profileId, string $attachmentId): Application|ResponseFactory|Response
    {
        $attachment = $request
            ->user()
            ->profile()
            ->findOrFail($profileId)
            ->attachments()
            ->findOrFail($attachmentId);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response("", 204);
    }
}
