<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectPlan\ProjectPlanResource;
use App\Models\B3Projects;
use App\Models\ProjectPlan;

class B3ProjectPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function b3ProjectPlan(B3Projects $b3Project)
    {
       // Fetch the collection of ProjectPlan instances related to $b3Project
       $projectPlans = $b3Project->projectPlan()->with('files')->get();

        // Return a JSON response using the ProjectPlanResource collection
        return ProjectPlanResource::collection($projectPlans);
    }
}
