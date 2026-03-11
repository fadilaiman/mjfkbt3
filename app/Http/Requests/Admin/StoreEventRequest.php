<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after:start_datetime'],
            'location' => ['nullable', 'string', 'max:500'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'max:10240'],
        ];
    }
}
