<?php

namespace App\Http\Requests\LOME;

use Illuminate\Foundation\Http\FormRequest;

class LOMEStoreRequest extends FormRequest
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
            // 'quantity' => 'required | numeric',
            'material_id' => 'required | numeric '
        ];
    }
}
