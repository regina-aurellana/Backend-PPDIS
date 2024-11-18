<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkScheduleItem\AddDurationRequest;
use App\Http\Requests\WorkScheduleItem\AddWorkScheduleItemRequest;
use App\Http\Requests\WorkScheduleItem\UpdateWorkScheduleItemRequest;
use App\Http\Services\DupaPerProject\WorkSchedule\WorkScheduleItemService;
use App\Models\Dupa;
use App\Models\ProgramOfWork;
use App\Models\Schedule;
use App\Models\SowCategory;
use App\Models\SowSubCategory;
use App\Models\UnitOfMeasurement;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkScheduleItemController extends Controller
{

    public function getContent($id = null)
    {
        $work_sched_items = WorkScheduleItem::with([
            'workSchedule' => function ($q) {
                $q->select(
                    'id',
                    'b3_project_id'
                );
            },
            'schedule' => function ($q) {
                $q->select(
                    'id',
                    'work_sched_item_id',
                    'week_no',
                    'day_no',
                    'duration_no',
                    'group_no'
                );
            },
            'workSchedule.b3Project' => function ($q) {
                $q->select(
                    'id',
                    'registry_no',
                    'project_title',
                    'project_nature_id',
                    'project_nature_type_id',
                    'location',
                    'status'
                );
                $q->with(['projectNature' => function ($q) {
                    $q->select('id', 'name');
                }]);

                $q->with(['projectNatureType' => function ($q) {
                    $q->select('id', 'name');
                }]);
            },
            'workSchedule.b3Project.ProgramOfWork' => function ($q) {
                $q->select(
                    'id',
                    'b3_project_id',
                    'created_at'
                );
            },
            'workSchedule.b3Project.ProgramOfWork.powTable' => function ($q) {
                $q->select(
                    'pow_tables.id',
                    'pow_tables.program_of_work_id',
                    'pow_tables.sow_category_id',
                    'sow_categories.name as sow_category_name',
                    'sow_categories.item_code as sow_category_item_code'
                )
                    ->join('sow_categories', 'sow_categories.id', 'pow_tables.sow_category_id');
            },
            'workSchedule.b3Project.ProgramOfWork.powTable.contents' => function ($q) {
                $q->select(
                    'pow_table_contents.id',
                    'pow_table_contents.pow_table_id',
                    'pow_table_contents.sow_category_id',
                    'pow_table_contents.sow_subcategory_id',
                    'sow_sub_categories.name as subcat_name',
                    'sow_sub_categories.item_code as subcat_item_code',
                )
                    ->join('sow_sub_categories', 'sow_sub_categories.id', 'pow_table_contents.sow_subcategory_id')
                    ->join('pow_table_content_dupas', 'pow_table_content_dupas.pow_table_content_id', 'pow_table_contents.id')
                    ->join('dupa_per_projects', 'dupa_per_projects.id', '=', 'pow_table_content_dupas.dupa_per_project_id');
            },
            'workSchedule.b3Project.ProgramOfWork.powTable.contents.dupaItemsPerProject' => function ($q) {
                $q->select(
                    'pow_table_content_dupas.id',
                    'pow_table_content_dupas.pow_table_content_id',
                    'pow_table_content_dupas.dupa_per_project_id',
                    'pow_table_content_dupas.quantity',
                    'unit_of_measurements.abbreviation as unit',
                    'dupa_per_projects.item_number',
                    'dupa_per_projects.description as description',
                    'dupa_per_projects.output_per_hour',
                )
                    ->join('dupa_per_projects', 'dupa_per_projects.id', 'pow_table_content_dupas.dupa_per_project_id')
                    ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupa_per_projects.unit_id');
            },
        ]);

        if ($id != null)
            $work_sched_items->where('id', $id);

        $result = $work_sched_items->get();

        return $result;
    }

    public function index()
    {
        try {
            $content = $this->getContent();

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function create()
    {
        //
    }

    public function storeDuration(AddDurationRequest $request, WorkScheduleItem $work_schedule_item)
    {
        try {
            $existing_schedule = Schedule::where('work_sched_item_id', $work_schedule_item->id)->exists();

            $total_duration = 0;
            foreach ($request['dates'] as $date) {
                $total_duration += (float)$date['duration_no'];
            }

            // CHECK IF THE SCHEDULE IS ALREADY EXIST WITH THE WORK SCHED ITEM ID
            if ($existing_schedule) {
                // CHECK IF THE CURRENT SPLIT_NO OF WORKSCHEDULEITEM IS NOT EQUAL TO THE USER REQUEST SPLIT_NO MEANING THERE IS A NEW SPLIT_NO
                if ($work_schedule_item->split_no != $request['split_no']) {
                    WorkScheduleItem::where('id', $work_schedule_item->id)
                        ->update([
                            'duration' => $request['duration'],
                            'split_no' => $request['split_no']
                        ]);

                    // CHECK IF THE ARRAY OF DURATION FROM THE USER IS EQUAL TO THE UPDATED/CURRENT DURATION OF WORKSCHEDULEITEM
                    if ($total_duration == $work_schedule_item->duration) {
                        Schedule::where('work_sched_item_id', $work_schedule_item->id)->delete();

                        $group_no = 1;
                        foreach ($request['dates'] as $date) {
                            Schedule::create([
                                'work_sched_item_id' => $work_schedule_item->id,
                                'week_no' => $date['week_no'],
                                'day_no' => $date['day_no'],
                                'duration_no' => $date['duration_no'],
                                'group_no' => $group_no,
                            ]);

                            $group_no++;
                        }
                    } else {
                        // IF THE DURATIONS ARE IN CONFLICT, RETURN ERROR MESSAGE
                        return response()->json([
                            'status' => 'Error',
                            'message' => 'The total of duration numbers (' . $total_duration . ') of the array is not equal to the duration (' . $work_schedule_item->duration . ') of the Work Schedule Item.'
                        ], 422);
                    }
                } else {
                    // IF THE SPLIT_NO ARE THE SAME, CHECK IF THE ARRAY OF DURATION IS EQUAL TO THE CURRENT DURATION OF WORKSCHEDULEITEM BEFORE UPDATING THE DURATION/SPLIT_NO TO AVOID CONFLICT
                    if ($total_duration == $work_schedule_item->duration) {
                        WorkScheduleItem::where('id', $work_schedule_item->id)
                            ->update([
                                'duration' => $request['duration'],
                                'split_no' => $request['split_no']
                            ]);
                    } else {
                        // IF THE DURATIONS ARE IN CONFLICT, RETURN ERROR MESSAGE
                        return response()->json([
                            'status' => 'Error',
                            'message' => 'The total of duration numbers (' . $total_duration . ') of the array is not equal to the duration (' . $work_schedule_item->duration . ') of the Work Schedule Item.'
                        ], 422);
                    }
                }

                return response()->json([
                    'status' => "Updated",
                    'message' => "Work Schedule Item Successfully Updated"
                ], 200);
            } else {
                // IF THE SCHEDULE DOES NOT YET TO EXIST, UPDATE THE WORKSCHEDULEITEM DURATION AND SPLIT NO AND CREATE THE SCHEDULES ACCORDING TO USER REQUEST
                WorkScheduleItem::where('id', $work_schedule_item->id)
                    ->update([
                        'duration' => $request['duration'],
                        'split_no' => $request['split_no']
                    ]);

                // CHECK TO SEE IF THE TOTAL_DURATION WHICH IS THE ARRAY FROM USER REQUEST EQUALS TO THE CURRENT DURATION OF THE WORKSCHEDULEITEM
                if ($total_duration == $work_schedule_item->duration) {
                    $group_no = 1;
                    foreach ($request['dates'] as $date) {
                        Schedule::create([
                            'work_sched_item_id' => $work_schedule_item->id,
                            'week_no' => $date['week_no'],
                            'day_no' => $date['day_no'],
                            'duration_no' => $date['duration_no'],
                            'group_no' => $group_no,
                        ]);

                        $group_no++;
                    }

                    return response()->json([
                        'status' => 'Added',
                        'message' => 'Work Schedule Item - Duration Successfully Added'
                    ], 200);
                } else {
                    // IF THE ARRAY OF DURATION DOES NOT EQUAL TO THE CURRENT DURATION OF WORKSCHEDULEITEM, RETURN AN ERROR MESSAGE
                    return response()->json([
                        'status' => 'Error',
                        'message' => 'The total of duration numbers (' . $total_duration . ') of the array is not equal to the duration (' . $work_schedule_item->duration . ') of the Work Schedule Item.'
                    ], 422);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(WorkScheduleItem $work_schedule_item)
    {
        try {
            $content = $this->getContent($work_schedule_item->id);

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit(WorkScheduleItem $work_schedule_item)
    {
        //
    }

    public function update(UpdateWorkScheduleItemRequest $request, WorkScheduleItemService $service)
    {
        try {
            $service->update($request);

            return response()->json([
                'status' => 'Updated',
                'message' => 'Work Schedule Item Duration Successfully Updated.'
            ], 200);
        } catch (\Throwable $th) {
            // Handle other types of errors
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(WorkScheduleItem $work_schedule_item)
    {
        try {
            $work_schedule_item->schedule()->delete();

            $work_schedule_item->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Work Schedule Item Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
