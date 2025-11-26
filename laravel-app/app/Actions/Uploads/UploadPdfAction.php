<?php

namespace App\Actions\Uploads;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadPdfAction
{
    public function handle(UploadedFile $file, ?string $uploadedBy = null): Upload
    {
        $id = (string) Str::uuid();
        $storedName = $id . '.pdf';

        $path = $file->storeAs('uploads', $storedName, 'public');
        if ($path === false) {
            throw new \RuntimeException('Не удалось сохранить файл.');
        }

        $upload = new Upload([
            'id' => $id,
            'stored_name' => $storedName,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => $uploadedBy ?: 'Гость',
            'mime' => $file->getMimeType() ?: 'application/pdf',
            'size' => $file->getSize(),
            'created_at' => Carbon::now(),
        ]);

        $upload->save();

        return $upload;
    }
}
