<?php

namespace App\Http\Requests\ProjectDuration;

use Illuminate\Foundation\Http\FormRequest;

class AddProjectDurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'work_sched_id' => 'required',
            'no_of_days' => 'required'
        ];
    }
}
