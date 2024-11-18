<?php

namespace App\Http\Requests\ABCContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateABCContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'abc_id' => 'required|integer'
        ];
    }
}
