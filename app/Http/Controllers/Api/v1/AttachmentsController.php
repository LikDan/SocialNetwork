<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\AttachmentRequest;
use App\Http\Resources\Api\v1\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    public function store(AttachmentRequest $request, string $profileId, string $postId)
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $profile->posts()->findOrFail($postId);

        $file = $request->file("file");

        $query = $profile->posts()->findOrFail($postId)->attachments();

        $max_attachments = config("custom.max_post_attachments");
        if ($query->count() >= $max_attachments)
            return response()->json(["error" => "Max attachments limit exceeded"], 400);

        $path = Storage::putFile('attachments', $file);

        $attachment = [
            "path" => $path,
            "display_name" => $file->getClientOriginalName(),
            "type" => $file->getClientMimeType()
        ];

        $attachment = $query->create($attachment);
        return AttachmentResource::make($attachment);
    }

    public function delete(Request $request, string $profileId, string $postId, string $attachment)
    {
        $profile = $request->user()->profile()->findOrFail($profileId);
        $profile->posts()->findOrFail($postId)->attachments()->findOrFail($attachment)->delete();

        return response()->json(["status" => "ok"]);
    }
}
