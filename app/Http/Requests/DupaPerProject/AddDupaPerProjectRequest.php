<?php

namespace App\Http\Requests\DupaPerProject;

use Illuminate\Foundation\Http\FormRequest;

class AddDupaPerProjectRequest extends FormRequest
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
            'dupas' => 'required|array',
            'dupas.*' => 'required|integer',
            'b3_project_id' => 'required|integer',
            'sow_category_id' => 'required|integer',
            'subcategory_id' => 'nullable',
            'dupa_per_project_group_id' => 'nullable',
            'item_number' => 'nullable',
            'description' => 'nullable',
            'unit_id' => 'nullable',
            'category_dupa_id' => 'nullable',
            'output_per_hour' => 'nullable',
            'direct_unit_cost' => 'nullable',
        ];
    }
}
