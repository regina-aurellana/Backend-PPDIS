<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class WorkScheduleItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'work_sched_id', 'id');
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class, 'work_sched_item_id', 'id')->orderBy('week_no');
    }

    public function workScheduleItem()
    {
        return $this->hasOne(DupaPerProject::class, 'dupa_id', 'id');
    }
}
