<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProgramOfWork\PowTableRequest;
use App\Models\PowTable;
use App\Models\PowTableContentDupa;


class PowTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pow = PowTable::with([
            'programOfWork' => function ($q) {
                $q->leftJoin('b3_projects', 'b3_projects.id', 'program_of_works.b3_project_id')
                    ->select('program_of_works.id', 'b3_projects.*');
            },
            'sowCategory'
        ])
            ->get();
        return $pow;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PowTable $powTable)
    {
        $powTable = PowTable::where('id', $powTable->id)
            ->with([
                'programOfWork' => function ($q) {
                    $q->leftJoin('b3_projects', 'b3_projects.id', 'program_of_works.b3_project_id')
                        ->select('program_of_works.id', 'b3_projects.*');
                },
                'sowCategory'
            ])
            ->first();

        return $powTable;
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
    public function destroy(PowTable $powTable)
    {
        try {
            $contents = $powTable->contents;

            foreach ($contents as $content) {
                $tes = PowTableContentDupa::where('pow_table_content_id', $content->id)->delete();

                $content->delete();
            }


            $powTable->delete();

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


    public function powTableID($program_of_work_id, $sow_cat_id)
    {
        $powTableId = PowTable::where('program_of_work_id', $program_of_work_id)
            ->where('sow_category_id', $sow_cat_id)
            ->first();

        return $powTableId;
    }


    public function getPowTablesByPowID($program_of_work_id)
    {
        $powTables = PowTable::where('program_of_works.b3_project_id', $program_of_work_id)
        ->join('program_of_works', 'program_of_works.id', 'pow_tables.program_of_work_id')
            ->with([
                'sowCategory' => function ($q) {
                    $q->select('id', "item_code", "name");
                },
            ])
            ->get();


        return $powTables;
    }
}
