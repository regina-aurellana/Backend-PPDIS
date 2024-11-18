<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\WorkScheduleItem;
use App\Http\Requests\Schedule\AddScheduleRequest;
use App\Http\Requests\Schedule\UpdateScheduleRequest;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedule = Schedule::with([
            'workScheduleItem' => function ($q) {
                $q->select(
                    'id',
                    'work_sched_id',
                    'dupa_id',
                    'duration'
                );
            }
        ])
            ->get();

        return response()->json($schedule);
    }

    public function create()
    {
        //
    }

    public function store(UpdateScheduleRequest $request)
    {
        try {
            if ($request['id'] != null) {
                Schedule::where('id', $request['id'])
                    ->update([
                        'work_sched_item_id' => $request['work_sched_item_id'],
                        'week_no' => $request['week_no'],
                        'day_no' => $request['day_no'],
                        'duration_no' => $request['duration_no'],
                        'group_no' => $request['group_no']
                    ]);

                return response()->json([
                    'status' => "Updated",
                    'message' => "Schedule Successfully Updated"
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function storeStartSchedule(Request $request)
    {
        try {
            info($request);
            $total_weeks = 0;
            $start_week = $request['start_week']; //3
            $start_day = $request['start_day']; // 19
            $group_no = 1;
            $duration = $request['duration']; // 70

            $first_week_duration = intval(((7 * $start_week) - 1) - $start_day);
            $no_of_weeks_with_five_days = intval(floor(($duration - $first_week_duration) / 5));
            $remaining_duration = intval(floor(($duration - $first_week_duration) % 5));
            $work_sched_item_id = $request['work_sched_item_id'];

            if ($remaining_duration <= 0) {
                $total_weeks = $no_of_weeks_with_five_days + 1; //
            } else {
                $total_weeks = $no_of_weeks_with_five_days + 2; // 
            }

            WorkScheduleItem::where('id', $work_sched_item_id)
                ->update([
                    'split_no' => $total_weeks
                ]);
            for ($i = $start_week; $i <= ($start_week - 1) + $total_weeks; $i++) {
                info($i);
                $group_no++;
                if ($i == $start_week) {
                    Schedule::create([
                        'work_sched_item_id' => $work_sched_item_id,
                        'week_no' => $i,
                        'day_no' => $start_day,
                        'duration_no' =>  $first_week_duration,
                        'group_no' => $group_no
                    ]);
                } else if ($i == ($start_week - 1) + $total_weeks && $remaining_duration != 0) {

                    Schedule::create([
                        'work_sched_item_id' => $work_sched_item_id,
                        'week_no' => $i,
                        'day_no' => (7 * $i) - 6,
                        'duration_no' =>  $remaining_duration,
                        'group_no' => $group_no
                    ]);
                } else {
                    Schedule::create([
                        'work_sched_item_id' => $work_sched_item_id,
                        'week_no' => $i,
                        'day_no' => (7 * $i) - 6,
                        'duration_no' => 5,
                        'group_no' => $group_no
                    ]);
                }
            }

            return response()->json([
                'status' => "Success",
                'message' => "Schedule Successfully Created"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(Schedule $schedule)
    {
        $schedule = Schedule::where('id', $schedule->id)
            ->with([
                'workScheduleItem' => function ($q) {
                    $q->select(
                        'id',
                        'work_sched_id',
                        'dupa_id',
                        'duration'
                    );
                }
            ])
            ->first();

        return response()->json($schedule);
    }

    public function edit(Schedule $schedule)
    {
        //
    }

    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Schedule Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
