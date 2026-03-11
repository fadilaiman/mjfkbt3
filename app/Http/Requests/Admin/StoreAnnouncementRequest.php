<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'category' => ['required', 'in:aktiviti,pengumuman,kebajikan,am'],
            'is_pinned' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ];
    }
}
