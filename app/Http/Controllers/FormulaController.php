<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Formula\FormulaRequest;
use App\Models\Formula;

class FormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formula = Formula::get();

        return $formula;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormulaRequest $request)
    {
        try {
            $formula = Formula::updateOrCreate(
                [ 'id' => $request['id']],
                [
                    'result' => $request['result'],
                    'formula' => $request['formula'],
                ]
            );

            if ($formula->wasRecentlyCreated) {
                return response()->json([
                    'status' => "Created",
                    'message' => "Formula is Successfully Created"
                ]);
            }else{
                return response()->json([
                    'status' => "Updated",
                    'message' => "Formula is Successfully Updated "
                ]);
            }


        } catch (\Throwable $th) {
            return response([
                'status'=> 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Formula $formula)
    {
        $formula = Formula::where('id', $formula->id)->first();

        return $formula;
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
    public function destroy(Formula $formula)
    {
        try {
            $formula->delete();

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
