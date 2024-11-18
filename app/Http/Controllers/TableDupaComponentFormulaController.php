<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableDupaComponentFormula\StoreDupaComponentFormulaRequest;
use Illuminate\Http\Request;

use App\Models\TableDupaComponentFormula;

class TableDupaComponentFormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $component_formula = TableDupaComponentFormula::with([
            'tableDupaComponent',
            'tableDupaComponent.dupa',
            'formula',
        ])->paginate(10);

        return $component_formula;
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
    public function store(StoreDupaComponentFormulaRequest $request)
    {
        try {
           $comp_formula = TableDupaComponentFormula::updateOrCreate(
               ['id' => $request['id']],
                [
                'table_dupa_component_id' => $request['table_dupa_component_id'],
                'formula_id' => $request['formula_id']
                ]
        );

        if ($comp_formula->wasRecentlyCreated) {
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
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TableDupaComponentFormula $table_dupa_component_formula)
    {
        $component_formula = TableDupaComponentFormula::where('id', $table_dupa_component_formula->id)
            ->with([
            'tableDupaComponent',
            'formula',
        ])->first();

        return $component_formula;
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
    public function destroy(TableDupaComponentFormula $table_dupa_component_formula)
    {
        try {

            $table_dupa_component_formula->delete();

             return response()->json([
                 'status' => "Deleted",
                 'message' => "Deleted Successfully"
             ]);
         } catch (\Throwable $th) {
             return response()->json([
                 'status' => "Error",
                 'message' => $th->getMessage()
             ]);
         }
    }
}
