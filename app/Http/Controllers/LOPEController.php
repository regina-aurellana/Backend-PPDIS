<?php

namespace App\Http\Controllers;

use App\Http\Requests\LOPE\StoreLOPERequest;
use App\Http\Requests\LOPE\UpdateLOPERequest;
use App\Http\Resources\LOPE\LOPEResource;
use App\Http\Resources\LOPE\LOPETableResource;
use App\Http\Services\DupaPerProject\LOPE\LOPEService;
use App\Models\B3Projects;
use App\Models\LOPE;
use Illuminate\Http\Request;

class LOPEController extends Controller
{

    public $service;

    public function __construct(LOPEService $service)
    {
        $this->service = $service;
    }
    
    public function index(B3Projects $b3_project)
    {
        try {

            return LOPETableResource::collection($this->service->index($b3_project));

        } catch (\Throwable $th) {
            info('index LOPE error: ' . $th->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    public function store(StoreLOPERequest $request, B3Projects $b3_project)
    {
        try {
            $this->service->store($request, $b3_project);

            return response()->json([
                'status' => 'Success',
                'message' => 'Saved Successfully'
            ]);
        } catch (\Throwable $th) {

            info('Store LOPE error: ' . $th->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    public function show(LOPE $lope)
    {
        try {

            return new LOPEResource($lope);

        } catch (\Throwable $th) {
            info('show LOPE error: ' . $th->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    public function update(UpdateLOPERequest $request, LOPE $lope)
    {
        try {
            $this->service->update($request, $lope);

            return response()->json([
                'status' => 'Success',
                'message' => 'Updated Successfully'
            ]);
        } catch (\Throwable $th) {

            info('Update LOPE error: ' . $th->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    public function destroy(LOPE $lope)
    {
        try {
            $this->service->destroy($lope);

            return response()->json([
                'status' => 'Success',
                'message' => 'Deleted successfully'
            ]);
        } catch (\Throwable $th) {

            info('Destroy LOPE error: ' . $th->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
