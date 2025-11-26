<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeatherUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temperature' => ['required', 'numeric', 'min:-99.99', 'max:99.99'],
            'humidity' => ['required', 'integer', 'min:0', 'max:100'],
            'pressure' => ['required', 'integer', 'min:0', 'max:2000'],
            'wind_speed' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'description' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:10'],
        ];
    }
}
