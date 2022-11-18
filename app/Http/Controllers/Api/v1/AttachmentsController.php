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
use Symfony\Component\HttpFoundation\JsonResponse;

class AttachmentsController extends Controller
{
    public function store(AttachmentRequest $request, string $profileId): JsonResponse | AttachmentResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);

        $file = $request->file("file");
        $path = Storage::putFile('attachments', $file);

        $attachment = [
            "path" => $path,
            "display_name" => $file->getClientOriginalName(),
            "type" => $file->getClientMimeType(),
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
