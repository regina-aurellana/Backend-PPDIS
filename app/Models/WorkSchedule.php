<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['work_sched_id', 'b3_project_id', 'dupa_per_project_group_id'];

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class);
    }

    public function group(){
        return $this->belongsTo(DupaPerProjectGroup::class, 'id');
    }

    public function projectDuration()
    {
        return $this->hasOne(ProjectDuration::class, 'work_sched_id', 'id');
    }

    public function workScheduleItem()
    {
        return $this->hasMany(WorkScheduleItem::class, 'work_sched_id');
    }
}
