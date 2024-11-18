<?php

namespace App\Http\Requests\DupaLaborPerProject;

use Illuminate\Foundation\Http\FormRequest;

class AddDupaLaborPerProjectRequest extends FormRequest
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
            'dupa_content_per_project_id' => 'required|integer',
            'labor_id' => 'required|integer',
            'no_of_person' => 'required|integer',
            'no_of_hour' => 'required|integer',
            'group' => 'nullable',
        ];
    }
}
