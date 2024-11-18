<?php

namespace App\Models;

use App\Models\TakeOff;
use App\Models\B3Projects;
use App\Models\ProgramOfWork;
use App\Models\DupaPerProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DupaPerProjectGroup extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dupaPerProject()
    {
        return $this->hasMany(DupaPerProject::class, 'dupa_per_project_group_id', 'id');
    }

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class, 'id');
    }

    public function takeOff()
    {
        return $this->hasMany(TakeOff::class, 'dupa_per_project_group_id', 'id');
    }

    public function programOfWork()
    {
        return $this->hasMany(ProgramOfWork::class, 'dupa_per_project_group_id', 'id');
    }
}
