<?php

namespace App\Http\Controllers;

use App\Models\LOME;
use App\Models\B3Projects;
use App\Http\Requests\LOME\LOMEStoreRequest;
use App\Http\Requests\LOME\LOMEUpdateRequest;
use App\Models\DupaPerProject;

class LOMEController extends Controller
{
    public function index(B3Projects $b3_project)
    {
        try {

            $b3_project_lome = LOME::where('b3_project_id', $b3_project->id)
                ->join('materials', 'lome.material_id', '=', 'materials.id')
                ->select('lome.*', 'materials.item_code', 'materials.name')
                ->get();

            return response()->json($b3_project_lome);
        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()]);
        }
    }

    public function store(LOMEStoreRequest $request, B3Projects $b3_project)
    {
        try {

            LOME::create([
                'b3_project_id' => $b3_project->id,
                'material_id' => $request->material_id,
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

    public function show(LOME $lome)
    {
        try {
            $_lome = LOME::where('id', $lome->id)->get();

            return response()->json($_lome);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function update(LOMEUpdateRequest $request, LOME $lome)
    {
        try {
            $lome->update([
                'quantity' => $request->quantity
            ]);
            return response()->json([
                'status' => 'Success',
                'message' => 'LOME Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy(LOME $lome)
    {
        try {

            $lome->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Deleted Successfully'
            ]);
        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
