<?php

namespace App\Http\Requests\ProjectPlan;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateProjectPlanRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this as needed for authorization checks
    }

    public function rules()
    {
        $isUpdate = $this->has('project_plans.0.id');

        $rules = [
            'project_plans.*.name' => 'required|string',
        ];

        if (!$isUpdate) {
            // Validation rules for creation
            $rules['project_plans.*.filepond'] = 'required|array';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        if(!empty($this->project_plans)) {
            info($this->project_plans);
            $ctr = 1;
            foreach($this->project_plans as $key => $value) {
                $messages['project_plans.'.$key . '.name'] = 'The name field in row ' .$ctr.' is required for each project plan.';
                $messages['project_plans.'.$key . '.filepond'] = 'The file in row ' .$ctr.' is required for each project plan.';
            
                $ctr++;
            }
        }

        return $messages;
        
    }
}
