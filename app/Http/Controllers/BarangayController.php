<?php

namespace App\Http\Controllers;

use App\Http\Requests\Barangay\AddBarangayRequest;
use App\Models\Barangay;
use Illuminate\Http\Request;

class BarangayController extends Controller
{
    public function index()
    {
        $barangay = Barangay::with(['district' => function ($q) {
            $q->select('id', 'name');
        }])->get();

        return response()->json($barangay);
    }

    public function create()
    {
        //
    }

    public function store(AddBarangayRequest $request)
    {
        try {
            if ($request['id'] == null) {
                Barangay::create([
                    'district_id' => $request['district_id'],
                    'name' => $request['name'],
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'Barangay Successfully Created'
                ]);
            } else {
                Barangay::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'district_id' => $request['district_id'],
                        'name' => $request['name'],
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'Barangay Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(Barangay $barangay)
    {
        try {
            $content = Barangay::where('id', $barangay->id)
                ->with(['district' => function ($q) {
                    $q->select('id', 'name');
                }])->first();

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

    public function destroy(Barangay $barangay)
    {
        try {
            $barangay->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Barangay Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
