<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\AttachmentRequest;
use App\Http\Resources\Api\v1\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttachmentsController extends Controller
{
    public function store(AttachmentRequest $request, string $profileId, string $postId): JsonResponse | AttachmentResource
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $post = $profile->posts()->findOrFail($postId);

        $file = $request->file("file");

        $max_attachments = config("custom.max_attachments");
        if ($post->attachments()->count() >= $max_attachments)
            return response()->json(["error" => "Max attachments limit exceeded"], 400);

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
        echo Storage::delete("attachments/file.txt");

        $profile = $request->user()->profile()->findOrFail($profileId);
        $attachment = $profile->posts()->findOrFail($postId)->attachments()->findOrFail($attachmentId);
        Storage::delete($attachment->path);

        if (!$attachment->delete()) throw new NotFoundHttpException();

        return response()->json(["status" => "ok"]);
    }
}
