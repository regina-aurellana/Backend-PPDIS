<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkSchedule\AddWorkscheduleRequest;
use App\Http\Services\DupaPerProject\WorkSchedule\WorkScheduleService;
use App\Models\B3Projects;
use App\Models\Dupa;
use App\Models\DupaPerProject;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleItem;
use App\Models\ProgramOfWork;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    public function getContent($id = null)
    {

        $work_schedule = WorkSchedule::with([
            'projectDuration',
            'b3Project' => function ($q) {
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
            'b3Project.ProgramOfWork' => function ($q) {
                $q->select(
                    'id',
                    'b3_project_id',
                    'created_at'
                );
            },


        ]);



        if ($id != null)
            $work_schedule->where('b3_project_id', $id)->with([
                'b3Project.dupaPerProjectGroup' => function ($q) {
                    $q->select('id', 'name', 'group_no', 'b3_project_id');
                }
            ]);

        $result = $work_schedule->get();

        // Manipulate the data

        $processedData = [];

        foreach ($result as $key => $workSchedule) {

            $b3Project = $workSchedule->b3Project;
            $projectDuration = $workSchedule->projectDuration;
            $programOfWork = $workSchedule->b3Project->programOfWork[$key];
            $powTable = [];
            $powTable = $programOfWork->powTable;
            $group =  $workSchedule->b3Project->dupaPerProjectGroup[$key];


            $contents = [];


            // $workSchedule->workScheduleItem;

            foreach ($workSchedule->b3Project->programOfWork[$key]->powTable as $powTables) {

                $part_number = $powTables->sowCategory;
                $contents = $powTables->contents;

                $dupaItemsPerProject = [];

                foreach ($contents as $key => $content) {

                    $part_letter = $content->sowSubcategory;
                    $dupaItemsPerProject = $content->dupaItemsPerProject;


                    foreach ($dupaItemsPerProject as $key => $item) {
                        $dupaPerProject = $item->dupaPerProject;

                        $measure = $dupaPerProject->measures;

                        $workScheduleItem = $dupaPerProject->workScheduleItem;

                        $schedules = $workScheduleItem->schedule;
                    }
                }
            }

            $formattedData = [
                'id' => $workSchedule->id,
                'project_duration' => $projectDuration,
                'b3_project_id' => $workSchedule->b3_project_id,
                'created_at' => $workSchedule->created_at,
                'updated_at' => $workSchedule->updated_at,
                'b3_project' => [
                    'id' => $b3Project->id,
                    'group' => $group,
                    'registry_no' => $b3Project->registry_no,
                    'project_title' => $b3Project->project_title,
                    'project_nature_id' => $b3Project->project_nature_id,
                    'project_nature_type_id' => $b3Project->project_nature_type_id,
                    'location' => $b3Project->location,
                    'status' => $b3Project->status,
                    'program_of_work' => [
                        'id' => $programOfWork->id,
                        'b3_project_id' => $programOfWork->b3_project_id,
                        'created_at' => $programOfWork->created_at,
                        'pow_table' => $powTable,

                    ]
                ],
            ];

            // Add the formatted data to the processed array
            $processedData[] = $formattedData;
        }


        return $processedData;
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

    public function store(AddWorkscheduleRequest $request, WorkScheduleService $service)
    {
        try {


            if ($request['id'] == null) {

                $service->store($request);

                return response()->json([
                    'status' => "Created",
                    'message' => "Work Schedule Successfully Created"
                ]);
            } else {

                $service->update($request);

                return response()->json([
                    'status' => "Updated",
                    'message' => "Work Schedule Successfully Updated"
                ]);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show($work_schedule)
    {
        try {

            $content = $this->getContent($work_schedule);

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(WorkSchedule $work_schedule)
    {
        try {
            $work_schedule->workScheduleItem->each(function ($item) {
                if ($item->schedule)
                    $item->schedule->delete();

                $item->delete();
            });

            $work_schedule->projectDuration()->delete();

            $work_schedule->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Work Schedule Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
