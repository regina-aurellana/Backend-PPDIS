<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectDuration\AddProjectDurationRequest;
use App\Models\ProjectDuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectDurationController extends Controller
{
    public function index()
    {
        $project_duration = ProjectDuration::with([
            'workSchedule' => function ($q) {
                $q->select(
                    'id',
                    'b3_project_id'
                );
            }
        ])
            ->get();

        return response()->json($project_duration);
    }

    public function create()
    {
        //
    }

    public function store(AddProjectDurationRequest $request)
    {
        try {
            if ($request['id'] == null) {
                ProjectDuration::create([
                    'work_sched_id' => $request['work_sched_id'],
                    'no_of_days' => $request['no_of_days'],
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'Project Duration Successfully Created'
                ]);
            } else {
                ProjectDuration::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'work_sched_id' => $request['work_sched_id'],
                        'no_of_days' => $request['no_of_days'],
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'Project Duration Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(ProjectDuration $project_duration)
    {
        try {
            $content = ProjectDuration::where('project_durations.id', $project_duration->id)
                ->with([
                    'workSchedule' => function ($q) {
                        $q->select(
                            'id',
                            'b3_project_id'
                        );
                    }
                ])
                ->first();

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit(ProjectDuration $project_duration)
    {
        //
    }

    public function update(Request $request, ProjectDuration $project_duration)
    {
        //
    }

    public function destroy(ProjectDuration $project_duration)
    {
        try {
            $project_duration->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Project Duration Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
