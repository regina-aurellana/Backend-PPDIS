<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectNatureType\AddProjectNatureTypeRequest;
use Carbon\Carbon;

use App\Models\ProjectNatureType;

class ProjectNatureTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proj_nature_type = ProjectNatureType::join('project_natures', 'project_natures.id', 'project_nature_types.project_nature_id')
            ->select('project_nature_types.*', 'project_natures.name as project_nature_name')
            ->get();

        return response()->json($proj_nature_type);
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
    public function store(AddProjectNatureTypeRequest $request)
    {
        try {
            $projectNatureType = ProjectNatureType::updateOrCreate(
                [
                    'id' => $request['id'],
                ],
                [
                    'name' => $request['name'],
                    'project_nature_id' => $request['project_nature_id'],
                ]
            );

            if ($projectNatureType->wasRecentlyCreated) {
                return response()->json([
                    'status' => "Success",
                    'message' => "Successfully Added Project Nature"
                ]);
            }

            return response()->json([
                'status' => "Updated",
                'message' => "Successfully Updated Project Nature"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "ERROR",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(ProjectNatureType $type)
    {
        $nature_type = $type->where('project_nature_types.id', $type->id)
            ->with('projectNature')
            ->get();

        return response()->json($nature_type);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
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
    public function destroy(ProjectNatureType $type)
    {
        try {
            $type->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Deleted Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Success',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function typeList(ProjectNatureType $type)
    {
        $nature_type = ProjectNatureType::where('project_nature_types.id', $type->id)
            ->join('project_natures', 'project_natures.id', 'project_nature_types.project_nature_id')
            ->select('project_nature_types.*', 'project_natures.name as project_nature_name')
            ->first();

        return response()->json($nature_type);
    }
}
