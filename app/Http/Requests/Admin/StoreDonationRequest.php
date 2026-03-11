<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'collected_amount' => ['required', 'numeric', 'min:0'],
            'contributor_count' => ['required', 'integer', 'min:0'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ];
    }
}
