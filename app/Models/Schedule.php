<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function workScheduleItem()
    {
        return $this->belongsTo(WorkScheduleItem::class, 'work_sched_item_id', 'id');
    }
}
