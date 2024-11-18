<?php

namespace App\Http\Requests\TakeOff;

use Illuminate\Foundation\Http\FormRequest;

class TakeOffRequest extends FormRequest
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
            'b3_project_id' => 'required',
            'limit' => 'nullable',
            'length' => 'nullable',
        ];
    }
}
