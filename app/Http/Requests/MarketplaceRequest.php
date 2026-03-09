<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketplaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rule = [
            'provider' => ['required', 'string'],
            'icon' => ['nullable'],
            'url' => ['nullable', 'string'],
        ];

        if ($this->method() == 'POST') {
            $rule['icon'] = ['required'];
        }

        return $rule;
    }
}
