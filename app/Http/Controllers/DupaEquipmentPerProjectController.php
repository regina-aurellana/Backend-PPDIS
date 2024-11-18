<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DupaContentPerProject;
use App\Models\DupaEquipmentPerProject;
use App\Http\Services\DupaPerProject\MER\MERService;
use App\Http\Requests\DupaEquipmentPerProject\AddDupaEquipmentPerProjectRequest;

class DupaEquipmentPerProjectController extends Controller
{
    public function index()
    {
        $dupa_equip_per_projects = DupaEquipmentPerProject::with([
            'equipment' => function ($q) {
                $q->select('id', 'item_code', 'name', 'hourly_rate');
            }
        ])
            ->get();


        return response()->json($dupa_equip_per_projects);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaEquipmentPerProjectRequest $request)
    {
        try {
            $dupa_equipment_per_project = DupaEquipmentPerProject::updateOrCreate(
                ['id' => $request['id']],
                [
                    'dupa_content_per_project_id' => $request['dupa_content_per_project_id'],
                    'equipment_id' => $request['equipment_id'],
                    'no_of_unit' => $request['no_of_unit'],
                    'no_of_hour' => $request['no_of_hour'],
                ]
            );

            //EQUIPMENT FOR MER
            $dupacontent_pp = DupaContentPerProject::with('dupaPerProject')->find($request['dupa_content_per_project_id']);
            $dupa_pp = $dupacontent_pp->dupaPerProject;
            $mer_service = new MERService;
            $mer_service->MERFromDupa([['equipment_id' =>  $request['equipment_id']]], $dupa_pp->b3_project_id);


            if ($dupa_equipment_per_project->wasRecentlyCreated) {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Equipment Per Project Succesfully Added'
                ]);
            } else {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Equipment Per Project Succesfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(DupaEquipmentPerProject $dupa_equipment_per_project)
    {
        $dupa_equipment = DupaEquipmentPerProject::where('id', $dupa_equipment_per_project->id)
            ->with([
                'equipment' => function ($q) {
                    $q->select('equip.id', 'equipment.id', 'equipment.hourly_rate', 'equipment.name', DB::raw('COALESCE(equip.final_price, (equip.no_of_unit * equip.no_of_hour * equipment.hourly_rate)) as equipment_amount'))
                        ->join('dupa_equipment_per_projects as equip', 'equipment.id', '=', 'equip.equipment_id');
                },
                'equipment.equipmentComponent' => function ($q) {
                    $q->select('id', 'equip_id', 'component_name');
                },

            ])
            ->first();

        return response()->json($dupa_equipment);
    }

    public function edit(DupaEquipmentPerProject $dupa_equipment_per_project)
    {
        //
    }

    public function update(Request $request, DupaEquipmentPerProject $dupa_equipment_per_project)
    {
        //
    }

    public function destroy(DupaEquipmentPerProject $dupa_equipment_per_project)
    {
        try {
            $dupa_equipment_per_project->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Equipment Per Project Succesfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
