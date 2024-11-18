<?php

namespace App\Http\Requests\Communication;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommunicationRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'communication_reference_number' => [ 'required', 'max:255', 'unique:communications,comms_ref_no' ],
            'communication_category_id' => [ 'required' ],
            'district_id' => [ 'required' ],
            'barangay_id' => [ 'required' ],
            'subject' => [ 'required', 'max:255' ],

            'routed_to_user_id' => [ 'required' ],
            'remarks' => [ 'nullable', 'max:255' ],
            'action_taken' => [ 'required', 'max:255' ],

            'attachments' => [ 'nullable' ]
        ];
    }
}
