<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DupaMaterial\AddDupaMaterialRequest;
use Illuminate\Support\Facades\DB;

use App\Models\DupaMaterial;

class DupaMaterialController extends Controller
{

    public function index()
    {
        $dupa_material = DupaMaterial::with(['material', 'dupaContent'])
        ->get();

        return response()->json($dupa_material);
    }


    public function create()
    {
        //
    }


    public function store(AddDupaMaterialRequest $request)
    {
        try {
            DupaMaterial::updateOrCreate(
                ['id' => $request['id']],
                [
                    'material_id' => $request['material_id'],
                    'dupa_content_id' => $request['dupa_content_id'],
                    'quantity' => $request['quantity'],
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


    public function show(DupaMaterial $dupamaterial)
    {
        $dupa_material = DupaMaterial::where('id', $dupamaterial->id)
        ->with([
            'material' => function($q){
                $q->select('materials.id', 'materials.unit_cost', 'materials.name', DB::raw('(dupa_materials.quantity * materials.unit_cost) as material_amount'))
                    ->join('dupa_materials', 'materials.id', '=', 'dupa_materials.material_id');
            }
        ])
        ->first();

        return response()->json($dupa_material);
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(DupaMaterial $dupamaterial)
    {
        try {
            $dupamaterial->delete();

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
