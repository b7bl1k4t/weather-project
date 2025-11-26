<?php

namespace App\Http\Controllers\Web;

use App\Actions\Uploads\DeleteUploadAction;
use App\Actions\Uploads\GetUploadPathAction;
use App\Actions\Uploads\UploadPdfAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPdfRequest;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadFormController extends Controller
{
    public function store(UploadPdfRequest $request, UploadPdfAction $action): RedirectResponse
    {
        $login = $request->session()->get('preferences.login') ?? $request->string('uploaded_by')->toString();
        $action->handle($request->file('file'), $login ?: null);

        return redirect()->route('home')->with('status', 'PDF загружен.');
    }

    public function destroy(string $id, DeleteUploadAction $action): RedirectResponse
    {
        $action->handle($id);

        return redirect()->route('home')->with('status', 'PDF удалён.');
    }

    public function download(string $id, GetUploadPathAction $action): BinaryFileResponse
    {
        $path = $action->handle($id);

        return response()->download($path);
    }
}
