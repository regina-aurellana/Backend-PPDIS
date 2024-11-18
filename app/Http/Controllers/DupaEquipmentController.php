<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DupaEquipment\AddDupaEquipmentRequest;
use Illuminate\Support\Facades\DB;

use App\Models\DupaEquipment;

class DupaEquipmentController extends Controller
{
    public function index()
    {
        $dupa_equip = DupaEquipment::with(['equipment'])
        ->get();


        return response()->json($dupa_equip);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaEquipmentRequest $request)
    {
        try {

            DupaEquipment::updateOrCreate(
                ['id' => $request['id']],
                [
                    'equipment_id' => $request['equipment_id'],
                    'dupa_content_id' => $request['dupa_content_id'],
                    'no_of_unit' => $request['no_of_unit'],
                    'no_of_hour' => $request['no_of_hour'],
                ]
            );

            return response()->json([
                'status' => 'Success',
                'message' => 'Added Succesfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(DupaEquipment $dupaequipment)
    {
        $dupa_equip = DupaEquipment::where('id', $dupaequipment->id)
        ->with([
                'equipment' => function($q){
                $q->select('equip.id', 'equipment.id', 'equipment.hourly_rate', 'equipment.name', DB::raw('(equip.no_of_unit * equip.no_of_hour * equipment.hourly_rate) as equipment_amount'))
                  ->join('dupa_equipment as equip', 'equipment.id', '=', 'equip.equipment_id');

            },

            'equipment.equipmentComponent',

        ])
        ->first();


        return response()->json($dupa_equip);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(DupaEquipment $dupaequipment)
    {
        try {
            $dupaequipment->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Deleted Succesfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
