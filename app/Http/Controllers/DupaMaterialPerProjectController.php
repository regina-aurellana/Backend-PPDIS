<?php

namespace App\Http\Controllers;

use App\Http\Requests\DupaMaterialPerProject\AddDupaMaterialPerProjectRequest;
use App\Http\Services\DupaPerProject\LOME\LOMEService;
use App\Models\DupaContentPerProject;
use App\Models\DupaMaterialPerProject;
use App\Models\DupaPerProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DupaMaterialPerProjectController extends Controller
{
    public function index()
    {
        $dupa_material_per_project = DupaMaterialPerProject::with([
            'material' => function ($q) {
                $q->select('id', 'item_code', 'name', 'unit', 'unit_cost');
            },
            'dupaContentPerProject' => function ($q) {
                $q->select('id', 'dupa_per_project_id', 'minor_tool_percentage', 'consumable_percentage', 'material_area', 'equipment_area');
            }
        ])
            ->get();

        return response()->json($dupa_material_per_project);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaMaterialPerProjectRequest $request)
    {
        try {
            $dupa_material_per_project = DupaMaterialPerProject::updateOrCreate(
                ['id' => $request['id']],
                [
                    'dupa_content_per_project_id' => $request['dupa_content_per_project_id'],
                    'material_id' => $request['material_id'],
                    'quantity' => $request['quantity'],
                ]
            );

            //MATERIALS FOR LOME //DEPRECATED ** FOR POSTERITY
            $dupacontent_pp = DupaContentPerProject::with('dupaPerProject')->find($request['dupa_content_per_project_id']);
            $dupa_pp = $dupacontent_pp->dupaPerProject;
            $lome_service = new LOMEService;
            $lome_service->LomeFromDupa($dupa_pp->b3_project_id);


            if ($dupa_material_per_project->wasRecentlyCreated) {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Material Per Project Succesfully Added'
                ]);
            } else {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Material Per Project Succesfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(DupaMaterialPerProject $dupa_material_per_project)
    {
        $dupa_material = DupaMaterialPerProject::where('id', $dupa_material_per_project->id)
            ->with([
                'material' => function ($q) {
                    $q->select('materials.id', 'materials.unit_cost', 'materials.name', DB::raw('COALESCE(dupa_material_per_projects.final_price, (dupa_material_per_projects.quantity * materials.unit_cost)) as material_amount'))
                        ->join('dupa_material_per_projects', 'materials.id', '=', 'dupa_material_per_projects.material_id');
                }
            ])
            ->first();

        return response()->json($dupa_material);
    }

    public function edit(DupaMaterialPerProject $dupa_material_per_project)
    {
        //
    }

    public function update(Request $request, DupaMaterialPerProject $dupa_material_per_project)
    {
        //
    }

    public function destroy(DupaMaterialPerProject $dupa_material_per_project)
    {
        try {
            $dupa_material_per_project->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Material Per Project Succesfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
