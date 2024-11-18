<?php

namespace App\Http\Requests\DupaMaterialPerProject;

use Illuminate\Foundation\Http\FormRequest;

class AddDupaMaterialPerProjectRequest extends FormRequest
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
            'material_id' => 'required|integer',
            'quantity' => 'required|integer',
        ];
    }
}
