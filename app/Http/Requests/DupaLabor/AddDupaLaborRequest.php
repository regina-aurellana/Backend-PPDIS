<?php

namespace App\Http\Requests\DupaLabor;

use Illuminate\Foundation\Http\FormRequest;

class AddDupaLaborRequest extends FormRequest
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
            'labor_id' => 'required',
            'no_of_person' => 'required',
            'no_of_hour' => 'required',
            'group' => 'nullable',
        ];
    }
}
