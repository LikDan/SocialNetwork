<?php

namespace App\Http\Controllers\Storage;

use App\Models\Attachment;
use Storage;

class ClearUnusedStorage
{
    public function __invoke()
    {
        $saveDate = config("custom.save_date", 24);

        $dateWhenRemove = new \DateTime();
        $dateWhenRemove->modify("-$saveDate hours");
        $attachments = Attachment::query()
            ->where("updated_at", "<", $dateWhenRemove)
            ->whereNull('attachable_type')
            ->whereNull('attachable_id')
            ->get();

        foreach ($attachments as $attachment) {
            Storage::delete($attachment->path);
            $attachment->delete();
        }
    }
}
