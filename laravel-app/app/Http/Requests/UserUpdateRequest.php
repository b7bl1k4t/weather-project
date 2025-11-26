<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'username' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'password' => ['sometimes', 'required', 'string', 'min:6'],
            'email' => ['sometimes', 'nullable', 'email', 'max:100'],
        ];
    }
}
