<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AddUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string',
            'username' => 'required|string',
            'position' => 'required|string',
            'password' => ['required_if:id,null', 'confirmed', 'max:255'], //Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            'password_confirmation' => 'required_if:id,null',
            'role_id' => 'required',
            'role_name' => 'nullable',
            'team_id' => 'nullable|required_if:role_name,engineer',
            'is_active' => 'required_unless:id,null'
        ];
    }
}
