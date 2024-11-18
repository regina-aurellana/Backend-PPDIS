<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramOfWork\AddProgramOfWorkRequest;
use App\Http\Requests\ProgramOfWork\UpdateProgramOfWorkRequest;
use App\Http\Services\DupaPerProject\ProgramOfWork\ProgramOfWorkService;
use App\Models\DupaPerProject;
use App\Models\DupaPerProjectGroup;
use App\Models\PowTable;
use App\Models\PowTableContent;
use App\Models\PowTableContentDupa;
use App\Models\ProgramOfWork;
use App\Models\SowCategory;
use App\Models\SowSubCategory;
use App\Models\TakeOff;
use Illuminate\Http\Request;

class ProgramOfWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $pow = ProgramOfWork::with(
                [
                    'b3Project' => function ($q) {
                        $q->select(
                            'id',
                            'registry_no',
                            'project_title',
                            'project_nature_id',
                            'project_nature_type_id',
                            'location',
                            'status'
                        );
                    },
                    'b3Project.projectNature' => function ($q) {

                        $q->select(
                            'id',
                            'name',
                        );
                    },
                    'b3Project.projectNatureType' => function ($q) {

                        $q->select(
                            'id',
                            'name',
                        );
                    }
                ]
            )
                ->select('id as program_of_work_id', 'b3_project_id')
                ->get();

            return response()->json($pow);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddProgramOfWorkRequest $request)
    {
        try {
            
            $service = new ProgramOfWorkService;

            $service->store($request);

            return response()->json([
                'status' => "Created",
                'message' => "Program of Work Successfully Created. Pow Table, Pow Table Content and Pow Table Content Dupa are also successfully created."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $pow = ProgramOfWork::with(
                [
                    'b3Project' => function ($q) {
                        $q->select(
                            'id',
                            'registry_no',
                            'project_title',
                            'project_nature_id',
                            'project_nature_type_id',
                            'location',
                            'status'
                        );
                    },
                    'b3Project.projectNature' => function ($q) {
                        $q->select(
                            'id',
                            'name',
                        );
                    },
                    'b3Project.projectNatureType' => function ($q) {
                        $q->select(
                            'id',
                            'name',
                        );
                    }
                ]
            )
                ->select('id as program_of_work_id', 'b3_project_id')
                ->where('program_of_works.b3_project_id', $id)
                ->first();

            return response()->json($pow);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramOfWorkRequest $request, ProgramOfWork $pow)
    {
        try {
            $pow->update([
                'b3_project_id' => $request->b3_project_id,
            ]);

            return response()->json([
                'status' => 'Success',
                'Message' => 'Program of Work Successfully Updated'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramOfWork $pow)
    {
        try {

            $pow->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Program of Work Successfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Success',
                'message' => $th->getMessage()
            ]);
        }
    }



    private function saveDupafromTakeOff($newpowID, $b3ID)
    {

        $b3_id = TakeOff::where('b3_project_id', $b3ID)->first();

        $takeoffTables = $b3_id->takeOffTable;

        foreach ($takeoffTables as $takeoffTable) {

            $dupa = $takeoffTable->dupa;
            $dupa_cost = $dupa->direct_unit_cost;
            $pow_table = PowTable::where('program_of_work_id', $newpowID)
                ->where('sow_category_id', $takeoffTable->sow_category_id)
                ->first();

            // $dupa_subcat_id = $this->getBaseParent($subcat, $test);

            $main_category = SowSubCategory::where('id', $dupa->subcategory_id)->first();

            $baseParent = $main_category->findBaseParentSubCategory($main_category);

            $test = $baseParent->id;

            $savePowContent = [
                'pow_table_id' => $pow_table->id,
                'sow_category_id' => $takeoffTable->sow_category_id,
                'sow_subcategory_id' => $test,
            ];

            $contentID = PowTableContent::insertGetId($savePowContent);

            $savePowContentDupa = [
                'pow_table_content_id' => $contentID,
                'dupa_id' => $takeoffTable->dupa_id,
                'quantity' => $takeoffTable->table_say,
                'total_estimated_direct_cost' => floatval($dupa_cost) * floatval($takeoffTable->table_say),
            ];
            // info($saveDupa);

            PowTableContentDupa::insert($savePowContentDupa);
        }
    }
}
