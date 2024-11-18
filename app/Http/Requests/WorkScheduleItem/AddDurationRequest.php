<?php

namespace App\Http\Requests\WorkScheduleItem;

use Illuminate\Foundation\Http\FormRequest;

class AddDurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration' => 'required|numeric',
            'split_no' => 'required|integer',
            'dates' => 'required|array',
            'dates.*.week_no' => 'required|integer',
            'dates.*.day_no' => 'required|integer',
        ];
    }
}
