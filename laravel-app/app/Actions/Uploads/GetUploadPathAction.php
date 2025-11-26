<?php

namespace App\Actions\Uploads;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetUploadPathAction
{
    public function handle(string $id): string
    {
        $upload = Upload::query()->findOrFail($id);
        $path = 'uploads/' . $upload->stored_name;

        if (!Storage::disk('public')->exists($path)) {
            throw new NotFoundHttpException('Файл отсутствует на диске.');
        }

        return Storage::disk('public')->path($path);
    }
}
