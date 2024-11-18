<?php

namespace App\Http\Requests\ProjectNatureType;

use Illuminate\Foundation\Http\FormRequest;

class AddProjectNatureTypeRequest extends FormRequest
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
            'project_nature_id' => 'required',
            'name' => 'required',
        ];
    }
}
