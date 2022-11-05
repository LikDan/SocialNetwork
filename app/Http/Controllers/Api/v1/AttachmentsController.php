<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\AttachmentRequest;
use App\Http\Resources\Api\v1\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class AttachmentsController extends Controller
{
    public function store(AttachmentRequest $request, string $profileId, string $postId): JsonResponse | AttachmentResource
    {
        $post = $request->user()->profile()->findOrFail($profileId)->posts()->findOrFail($postId);

        $max_attachments = config("custom.max_attachments");
        if ($post->attachments()->count() >= $max_attachments)
            return response()->json(["error" => "Max attachments limit exceeded"], 400);

        $file = $request->file("file");
        $path = Storage::putFile('attachments', $file);

        $attachment = [
            "path" => $path,
            "display_name" => $file->getClientOriginalName(),
            "type" => $file->getClientMimeType()
        ];

        $attachment = $post->attachments()->create($attachment);
        return AttachmentResource::make($attachment);
    }

    public function delete(Request $request, string $profileId, string $postId, string $attachmentId): JsonResponse
    {
        $attachment = $request
            ->user()
            ->profile()
            ->findOrFail($profileId)
            ->posts()
            ->findOrFail($postId)
            ->attachments()
            ->findOrFail($attachmentId);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response()->json(["status" => "ok"]);
    }
}
