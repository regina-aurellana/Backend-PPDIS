<?php

namespace App\Http\Requests\LOPE;

use Illuminate\Foundation\Http\FormRequest;

class StoreLOPERequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => 'required | numeric',
            'key_personnel' => 'required | max:255',
            'quantity' => 'required | numeric',
        ];
    }
}
