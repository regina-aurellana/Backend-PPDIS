<?php

namespace App\Http\Resources\ProjectPlan;

use App\Http\Resources\ProjectPlanFileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectPlanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'b3_project_id' => $this->b3_project_id, // Include b3_project_id
            'name' => $this->name,
            // Include files relationship if loaded
            'file' => ProjectPlanFileResource::collection($this->whenLoaded('files'))
            // Add other fields you want to include
        ];
    }
}
