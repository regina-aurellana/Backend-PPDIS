<?php

namespace App\Http\Requests\DupaPerProjectGroup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDupaPerProjectGroupRequest extends FormRequest
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
            // 'b3_project_id' => 'required|integer|exists:b3_projects,id',
            'name' => 'required|max:255'
        ];
    }
}