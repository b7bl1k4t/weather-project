<?php

namespace App\Http\Controllers\Api;

use App\Actions\Uploads\DeleteUploadAction;
use App\Actions\Uploads\GetUploadPathAction;
use App\Actions\Uploads\ListUploadsAction;
use App\Actions\Uploads\UploadPdfAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPdfRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadController extends Controller
{
    public function index(ListUploadsAction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(),
        ]);
    }

    public function store(UploadPdfRequest $request, UploadPdfAction $action): JsonResponse
    {
        $upload = $action->handle($request->file('file'), $request->string('uploaded_by')->toString() ?: null);

        return response()->json(['data' => $upload], 201);
    }

    public function destroy(string $id, DeleteUploadAction $action): JsonResponse
    {
        $action->handle($id);

        return response()->json(null, 204);
    }

    public function download(string $id, GetUploadPathAction $action): BinaryFileResponse
    {
        $path = $action->handle($id);

        return response()->download($path);
    }
}
