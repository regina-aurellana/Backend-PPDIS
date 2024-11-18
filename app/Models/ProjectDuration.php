<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDuration extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'work_sched_id', 'id');
    }
}
