<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPlanFile extends Model
{
    use HasFactory;

    protected $guarded  = [];

    public function projectPlan()
    {
        return $this->belongsTo(ProjectPlan::class);
    }
}
