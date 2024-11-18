<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitOfMeasurement;
use App\Http\Requests\Measurement\UnitOfMeasurementRequest;

use Carbon\Carbon;

class UnitOfMeasurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $measure = UnitOfMeasurement::get();

        return response()->json($measure);
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
    public function store(UnitOfMeasurementRequest $request)
    {
        try {

            $mes = UnitOfMeasurement::updateOrCreate(
                ['id' => $request['measurement_id']],
                [
                    'name' => $request['name'],
                    'abbreviation' => $request['abbreviation'],
                ]
            );
            if ($mes->wasRecentlyCreated) {
                return response()->json([
                    'status' => "Created",
                    'message' => "Measurement Successfully Created"
                ]);
            } else {
                return response()->json([
                    'status' => "Updated",
                    'message' => "Measurement Successfully Updated "
                ]);
            }
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UnitOfMeasurement $measurement)
    {
        $measure = UnitOfMeasurement::where('id', $measurement->id)
            ->with('dupa')
            ->first();

        return response()->json($measure);
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
    public function destroy(UnitOfMeasurement $measurement)
    {
        try {
            $measurement->delete();

            return response()->json([
                'status' => "Success",
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
