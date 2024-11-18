<?php

namespace App\Http\Controllers;

use App\Models\MER;
use App\Models\B3Projects;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MER\MERStoreRequest;
use App\Http\Requests\MER\MERUpdateRequest;

class MERController extends Controller
{

    public function index(B3Projects $b3_project)
    {
        try {
            $b3_project_mer = MER::where('b3_project_id', $b3_project->id)
                ->join('equipment', 'mer.equipment_id', '=', 'equipment.id')
                ->select('mer.*', 'equipment.item_code', 'equipment.name', 'equipment.hourly_rate')
                ->get();
            return response()->json($b3_project_mer);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    public function store(MERStoreRequest $request, B3Projects $b3_project)
    {
        try {
            MER::create([
                'b3_project_id' => $b3_project->id,
                'equipment_id' => $request->equipment_id,
                'quantity' => '1'
            ]);
            return response()->json([
                'status' => 'Success',
                'message' => 'Created Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }


    public function show(MER $mer)
    {
        try {
            $_mer = MER::where('id', $mer->id)->get();

            return response()->json($_mer);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function update(MERUpdateRequest $request, MER $mer)
    {
        try {
            $mer->update([
                'quantity' => $request->quantity
            ]);
            return response()->json([
                'status' => 'Success',
                'message' => 'MER Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }


    public function destroy(MER $mer)
    {
        try {

            $mer->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Deleted Successfully'
            ]);
        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
