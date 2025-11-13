<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeatherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temperature' => ['required', 'numeric', 'between:-100,100'],
            'humidity' => ['required', 'integer', 'between:0,100'],
            'pressure' => ['required', 'integer', 'between:800,1100'],
            'wind_speed' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:10'],
            'observed_at' => ['nullable', 'date'],
        ];
    }
}
