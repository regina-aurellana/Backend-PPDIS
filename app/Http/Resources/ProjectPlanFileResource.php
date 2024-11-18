<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectPlanFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'url' => route('project-plan.file', [
                'b3Project' => $this->projectPlan->b3_project_id,
                'projectPlanFile' => $this->id
            ])
        ];
    }
}
