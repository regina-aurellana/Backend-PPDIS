<?php

namespace App\Http\Requests\DupaEquipment;

use Illuminate\Foundation\Http\FormRequest;

class AddDupaEquipmentRequest extends FormRequest
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
            'dupa_content_id' => 'required',
            'equipment_id' => 'required',
            'no_of_unit' => 'required',
            'no_of_hour' => 'required',
        ];
    }
}
