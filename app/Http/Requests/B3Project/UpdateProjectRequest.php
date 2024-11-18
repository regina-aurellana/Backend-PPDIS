<?php

namespace App\Http\Requests\B3Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'registry_no' => 'required',
            'project_title' => 'required',
            'project_nature_id' => 'required',
            'project_nature_type_id' => 'required',
            'concerned_division' => 'required|max:255',
            'barangay_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'duration' => 'nullable',
            'contractor' => 'nullable',
            'location' => 'required',
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        $messages = [];

        $messages["project_nature_id.required"] = "The project nature field is required.";
        $messages["project_nature_type_id.required"] = "The project nature type field is required.";

        return $messages;
    }
}
