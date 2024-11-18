<?php

namespace App\Models;

use App\Models\ABC;
use App\Models\LOME;
use App\Models\LOPE;

use App\Models\TakeOff;
use App\Models\WorkSchedule;
use App\Models\ProgramOfWork;
use App\Models\ProjectNature;
use App\Models\DupaPerProject;
use App\Models\ProjectNatureType;
use App\Models\DupaPerProjectGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class B3Projects extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function projectNature()
    {
        return $this->belongsTo(ProjectNature::class);
    }

    public function projectNatureType()
    {
        return $this->belongsTo(ProjectNatureType::class);
    }

    public function takeOff()
    {
        return $this->hasOne(TakeOff::class, 'b3_project_id', 'id');
    }

    public function programOfWork()
    {
        return $this->hasMany(ProgramOfWork::class, 'b3_project_id', 'id');
    }

    public function abc()
    {
        return $this->hasOne(ABC::class, 'b3_project_id', 'id');
    }

    public function workSchedule()
    {
        return $this->hasMany(WorkSchedule::class, 'b3_project_id', 'id');
    }

    public function dupaPerProject()
    {
        return $this->hasMany(DupaPerProject::class, 'b3_project_id', 'id');
    }

    public function dupaPerProjectGroup()
    {
        return $this->hasMany(DupaPerProjectGroup::class, 'b3_project_id', 'id');
    }

    public function lope()
    {
        return $this->hasMany(LOPE::class, 'b3_project_id', 'id');
    }

    public function lome()
    {
        return $this->hasMany(LOME::class, 'b3_project_id', 'id');
    }

    public function projectPlan()
    {
        return $this->hasOne(ProjectPlan::class, 'b3_project_id', 'id');
    }
}
