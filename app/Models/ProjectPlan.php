<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPlan extends Model
{
    use HasFactory;

    protected $guarded  = [];

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectPlanFile::class, 'project_plan_id', 'id');
    }
}
