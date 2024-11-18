<?php

namespace App\Http\Requests\WorkScheduleItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkScheduleItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'id' => 'required|integer',
            // 'work_sched_id' => 'required|integer',
            // 'dupa_id' => 'required|integer',
            'duration' => 'required|numeric',
            // 'split_no' => 'required|integer'
        ];
    }
}
