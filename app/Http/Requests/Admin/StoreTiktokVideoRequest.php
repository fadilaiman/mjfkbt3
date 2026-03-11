<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTiktokVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tiktok_url' => ['required', 'url', 'max:1000'],
            'title' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
