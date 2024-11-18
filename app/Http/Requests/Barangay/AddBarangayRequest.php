<?php

namespace App\Http\Requests\Barangay;

use Illuminate\Foundation\Http\FormRequest;

class AddBarangayRequest extends FormRequest
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
            'id' => 'nullable',
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string'
        ];
    }
}
