<?php

namespace App\Http\Requests\DupaContentPerProject;

use Illuminate\Foundation\Http\FormRequest;

class AddMinorToolPercentageRequest extends FormRequest
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
            // 'dupa_per_project_id' => 'required|integer',
            'minor_tool_percentage' => 'required|numeric',
        ];
    }
}
