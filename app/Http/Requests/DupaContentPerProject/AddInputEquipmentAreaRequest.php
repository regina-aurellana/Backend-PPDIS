<?php

namespace App\Http\Requests\DupaContentPerProject;

use Illuminate\Foundation\Http\FormRequest;

class AddInputEquipmentAreaRequest extends FormRequest
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
            'area' => 'required|numeric'
        ];
    }
}
