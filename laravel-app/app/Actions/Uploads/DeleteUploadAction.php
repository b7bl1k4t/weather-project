<?php

namespace App\Actions\Uploads;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class DeleteUploadAction
{
    public function handle(string $id): void
    {
        $upload = Upload::query()->findOrFail($id);

        $path = 'uploads/' . $upload->stored_name;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $upload->delete();
    }
}
