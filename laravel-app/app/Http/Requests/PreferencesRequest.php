<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:40'],
            'theme' => ['required', Rule::in(['light', 'dark', 'contrast'])],
            'language' => ['required', Rule::in(['ru', 'en', 'es'])],
        ];
    }
}
