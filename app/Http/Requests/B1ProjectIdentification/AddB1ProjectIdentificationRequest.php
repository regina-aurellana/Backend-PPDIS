<?php

namespace App\Http\Requests\B1ProjectIdentification;

use Illuminate\Foundation\Http\FormRequest;

class AddB1ProjectIdentificationRequest extends FormRequest
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
            'site_inspection_report_id' => 'required|integer|exists:site_inspection_reports,id',
            'communication_id' => 'required|integer|exists:communications,id',
            'project_identification_no' => 'nullable',
            'initial_project_name' => 'required|string|max:255',
            'address' => 'required|max:255',
            'requesting_party' => 'required|string|max:255',
            'project_nature_id' => 'required|integer|exists:project_natures,id',
            'project_nature_type_id' => 'required|integer|exists:project_nature_types,id',
            'reason' => 'required',
            'existing_condition' => 'required|string|max:255',
            'estimated_beneficiary' => 'required|string|max:255',
            'recommendation' => 'required',
            'contact_no' => 'required|max:15',
            'status' => 'nullable'
        ];
    }
}
