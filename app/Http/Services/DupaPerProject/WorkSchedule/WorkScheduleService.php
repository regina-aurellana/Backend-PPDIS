<?php

namespace App\Http\Services\DupaPerProject\WorkSchedule;

use App\Models\WorkSchedule;
use App\Models\ProgramOfWork;
use App\Models\DupaPerProject;
use App\Models\ProjectDuration;
use App\Models\WorkScheduleItem;

class WorkScheduleService
{

    public function store($request)
    {

        $b3_projects = ProgramOfWork::where('b3_project_id', $request['b3_project_id'])
            ->with([
                'powTable' => function ($q) {
                    $q->select('id', 'program_of_work_id', 'sow_category_id');
                },
                'powTable.contents' => function ($q) {
                    $q->select('id', 'pow_table_id', 'sow_category_id', 'sow_subcategory_id');
                },
                'powTable.contents.dupaItemsPerProject' => function ($q) {
                    $q->select('id', 'pow_table_content_id', 'dupa_per_project_id', 'quantity');
                },
            ])
            ->get();


        $estimated_project_duration = 0;
        $work_schedule_ids = [];

        foreach ($b3_projects as $b3_project) {

            $flag = WorkSchedule::where('dupa_per_project_group_id', $b3_project->dupa_per_project_group_id)->first();

            if (!$flag) {
                $pow_tables = $b3_project->powTable;


                $work_schedule = WorkSchedule::create([
                    'b3_project_id' => $request['b3_project_id'],
                    'dupa_per_project_group_id' => $b3_project->dupa_per_project_group_id,
                ]);

                $work_schedule_ids[] = $work_schedule->id;
                foreach ($pow_tables as $pow_table) {
                    $contents = $pow_table->contents;


                    info($contents);
                    foreach ($contents as $content) {
                        $dupa_items = $content->dupaItemsPerProject;

                        foreach ($dupa_items as $dupa_item) {

                            $dupa = DupaPerProject::where('id', $dupa_item->dupa_per_project_id)->first();


                            $output_per_hour = $dupa->output_per_hour;
                            $quantity = $dupa_item->quantity;

                            $duration = intval(round(($output_per_hour * $quantity) / 8, 2));
                            $estimated_project_duration += $duration;


                            WorkScheduleItem::create([
                                'work_sched_id' => $work_schedule->id,
                                'dupa_per_project_id' => $dupa_item->dupa_per_project_id,
                                'duration' => $duration,
                                'split_no' => "1"
                            ]);
                        }
                    }
                }
            }
        }

        foreach ($work_schedule_ids as $ids) {
            ProjectDuration::create([
                'work_sched_id' => $ids,
                'no_of_days' =>  intval(ceil($estimated_project_duration)),
            ]);
        }
    }

    public function update($request)
    {
        $pow = ProgramOfWork::where('b3_project_id', $request['b3_project_id'])->first();

        WorkSchedule::updateOrCreate(
            ['id' => $request['id']],
            [
                'b3_project_id' => $request['b3_project_id'],
                'dupa_per_project_group_id' => $pow->dupa_per_project_group_id,
            ]
        );
    }
}
