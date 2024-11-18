<?php

namespace App\Http\Services\DupaPerProject\WorkSchedule;

use App\Models\DupaPerProject;
use App\Models\ProgramOfWork;
use App\Models\ProjectDuration;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleItem;

class WorkScheduleItemService
{
    public function update($request)
    {
        // info($request);
        WorkScheduleItem::updateOrCreate(
            ['id' => $request['work_schedule_item_id']],
            [
                'duration' => $request['duration'],
            ]
        );

        $new_estimated_duration = WorkScheduleItem::where('work_sched_id', $request['work_schedule_id'])->pluck('duration')->toArray();

        ProjectDuration::where('work_sched_id', $request['work_schedule_id'])->update([
            'no_of_days' => array_sum($new_estimated_duration),
        ]);
    }
}
