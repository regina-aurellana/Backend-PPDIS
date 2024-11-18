<?php

namespace App\Http\Requests\SiteInspectionReport;

use Illuminate\Foundation\Http\FormRequest;

class AddSiteInspectionReportRequest extends FormRequest
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
            'id' => 'nullable|integer',
            'communication_id' => 'required|integer|exists:communications,id',
            'sir_no' => 'nullable',
            'project_title' => 'required|string|max:255',
            'project_location' => 'required|string|max:255',
            'findings' => 'required',
            'recommendation' => 'required',
            'status' => 'nullable',
        ];
    }
}
