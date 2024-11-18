<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'work_sched_item_id' => 'required|integer',
            'week_no' => 'required|integer',
            'day_no' => 'required|integer',
            'duration_no' => 'required|integer',
            'group_no' => 'required|integer'
        ];
    }
}
