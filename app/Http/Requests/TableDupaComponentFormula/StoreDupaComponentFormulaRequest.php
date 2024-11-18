<?php

namespace App\Http\Requests\TableDupaComponentFormula;

use Illuminate\Foundation\Http\FormRequest;

class StoreDupaComponentFormulaRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'formula_id' => 'required',
            'table_dupa_component_id' => 'required',
        ];
    }
    
    public function messages(): array
    {
        return [
            'formula_id.required' => 'Formula is required',
            'table_dupa_component_id' => 'Dupa Component is required',
        ];
    }
}
