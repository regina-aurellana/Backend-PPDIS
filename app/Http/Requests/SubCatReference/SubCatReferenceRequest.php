<?php

namespace App\Http\Requests\SubCatReference;

use Illuminate\Foundation\Http\FormRequest;

class SubCatReferenceRequest extends FormRequest
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
            'sow_subcat_id' => 'required',
            'parent_id' => 'required',
        ];
    }
}
