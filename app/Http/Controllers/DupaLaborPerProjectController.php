<?php

namespace App\Http\Controllers;

use App\Http\Requests\DupaLaborPerProject\AddDupaLaborPerProjectRequest;
use App\Models\DupaLaborPerProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DupaLaborPerProjectController extends Controller
{
    public function index()
    {
        $dupa_labor_per_projects = DupaLaborPerProject::with([
            'dupaContentPerProject' => function ($q) {
                $q->select('id', 'dupa_per_project_id', 'minor_tool_percentage', 'consumable_percentage', 'material_area', 'equipment_area');
            },
            'labor' => function ($q) {
                $q->select('id', 'item_code', 'designation', 'hourly_rate');
            }
        ])
            ->get();

        return response()->json($dupa_labor_per_projects);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaLaborPerProjectRequest $request)
    {
        try {
            $dupa_labor_per_project = DupaLaborPerProject::updateOrCreate(
                ['id' => $request['id']],
                [
                    'dupa_content_per_project_id' => $request['dupa_content_per_project_id'],
                    'labor_id' => $request['labor_id'],
                    'no_of_person' => $request['no_of_person'],
                    'no_of_hour' => $request['no_of_hour'],
                    'group' => $request['group'],
                ]
            );

            if ($dupa_labor_per_project->wasRecentlyCreated) {
                return response()->json([
                    'id' => $dupa_labor_per_project->id,
                    'group' => $dupa_labor_per_project->group,
                    'status' => 'Created',
                    'message' => 'Dupa Labor Per Project Successfully Created'
                ]);
            } else {
                return response()->json([
                    'id' => $dupa_labor_per_project->id,
                    'group' => $dupa_labor_per_project->group,
                    'status' => 'Updated',
                    'message' => 'Dupa Labor Per Project Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(DupaLaborPerProject $dupa_labor_per_project)
    {
        $dupa_labor = DupaLaborPerProject::where('id', $dupa_labor_per_project->id)
            ->with([
                'labor' => function ($q) {
                    $q->select('dupa_labor_per_projects.id', 'labors.designation', 'labors.hourly_rate', DB::raw('COALESCE(dupa_labor_per_projects.final_price, (dupa_labor_per_projects.no_of_person * dupa_labor_per_projects.no_of_hour * labors.hourly_rate)) as labor_amount'))
                        ->join('dupa_labor_per_projects', 'labors.id', '=', 'dupa_labor_per_projects.labor_id');
                }
            ])
            ->first();

        return response()->json($dupa_labor);
    }

    public function edit(DupaLaborPerProject $dupa_labor_per_project)
    {
        //
    }

    public function update(Request $request, DupaLaborPerProject $dupa_labor_per_project)
    {
        //
    }

    public function destroy(DupaLaborPerProject $dupa_labor_per_project)
    {
        try {
            $id = $dupa_labor_per_project->id;
            $group = $dupa_labor_per_project->group;
            $dupa_labor_per_project->delete();

            return response()->json([
                'id' => $id,
                'group' => $group,
                'status' => 'Success',
                'message' => 'Dupa Labor Per Project Deleted Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
