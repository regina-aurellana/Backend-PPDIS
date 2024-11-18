<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableDupaComponent;
use App\Models\TableDupaComponentFormula;
use App\Models\SowCategory;
use App\Models\Dupa;

class TableDupaComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $component = TableDupaComponent::join('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
        ->leftJoin('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
        ->leftJoin('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
        ->select(
            'table_dupa_components.id',
            'table_dupa_components.dupa_id',
            'table_dupa_components.name',
            'dupas.description',
            'formulas.formula',
            'formulas.result',

            \DB::raw("CONCAT(dupas.description, '. ', COALESCE(table_dupa_components.name, '')) as concatenated_component_name")
            )
        ->get();

        return $component;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $comp = TableDupaComponent::updateOrCreate(
                ['id' => $request['id']],
                [
                'dupa_id' => $request['dupa_id'],
                'name' => $request['name'],
                ]
        );

        if ($comp->wasRecentlyCreated) {
            return response()->json([
                'status' => "Created",
                'message' => "Dupa Component is Successfully Created"
            ]);
        }else{
            return response()->json([
                'status' => "Updated",
                'message' => "Dupa Component is Successfully Updated "
            ]);
        }


        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TableDupaComponent $table_dupa_component)
    {
        $dupa_component =TableDupaComponent::where('table_dupa_components.id', $table_dupa_component->id)
        ->leftJoin('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
        ->leftJoin('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
        ->leftJoin('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
        ->select(
            'table_dupa_components.id',
            'table_dupa_components.dupa_id',
            'table_dupa_components.name as name',
            'dupas.description as dupa_name',
            'formulas.formula',
            'formulas.result',
            \DB::raw("CONCAT(dupas.description, '. ', COALESCE(table_dupa_components.name, '')) as concatenated_component_name")
            )
        ->first();


        return $dupa_component;






    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableDupaComponent $table_dupa_component)
    {
        try {


            $table_dupa_component->delete();


            return response()->json([
                'status' => "Deleted",
                'message' => "Deleted Successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => "Cannot be deleted due to associated content"
            ]);
        }
    }

    public function tableDupaComponentBySowCat($sowcat)
    {
        $dupa_component =TableDupaComponent::where('sow_sub_categories.sow_category_id', $sowcat)
        ->leftJoin('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
        ->leftJoin('sow_sub_categories', 'sow_sub_categories.id', 'dupas.subcategory_id')
        ->leftJoin('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
        ->leftJoin('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
        ->select(
            'table_dupa_components.id',
            'table_dupa_components.dupa_id',
            'table_dupa_components.name as name',
            'dupas.description as dupa_name',
            'formulas.formula',
            'formulas.result',
            \DB::raw("CONCAT(dupas.description, '. ', COALESCE(table_dupa_components.name, '')) as concatenated_component_name")
            )
        ->first();


        return $dupa_component;


    }


}
