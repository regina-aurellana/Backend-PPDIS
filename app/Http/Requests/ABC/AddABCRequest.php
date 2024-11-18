<?php

namespace App\Http\Requests\ABC;

use Illuminate\Foundation\Http\FormRequest;

class AddABCRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'b3_project_id' => 'required|integer',
        ];
    }
}
