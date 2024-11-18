<?php

namespace App\Http\Requests\MER;

use Illuminate\Foundation\Http\FormRequest;

class MERUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => 'numeric',
            // 'hourly_rate' => 'numeric'
        ];
    }
}