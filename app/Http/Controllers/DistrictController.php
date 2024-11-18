<?php

namespace App\Http\Controllers;

use App\Http\Requests\District\AddDistrictRequest;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index()
    {
        $district = District::with(['barangay' => function ($q) {
            $q->select('id', 'district_id', 'name');
        }])->get();

        return response()->json($district);
    }

    public function create($request)
    {
        //
    }

    public function store(AddDistrictRequest $request)
    {
        try {
            if ($request['id'] == null) {
                District::create([
                    'name' => $request['name'],
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'District Successfully Created'
                ]);
            } else {
                District::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'name' => $request['name'],
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'District Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(District $district)
    {
        try {
            $content = District::where('id', $district->id)
                ->with(['barangay' => function ($q) {
                    $q->select('id', 'district_id', 'name');
                }])
                ->get();

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(District $district)
    {
        try {
            $district->barangay->each(function ($item) {
                $item->delete();
            });

            $district->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'District Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
