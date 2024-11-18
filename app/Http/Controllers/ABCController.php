<?php

namespace App\Http\Controllers;

use App\Http\Requests\ABC\AddABCRequest;
use App\Http\Services\DupaPerProject\ABC\ABCService;
use App\Models\ABC;
use App\Models\ABCContent;
use App\Models\B3Projects;
use App\Models\ProgramOfWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ABCController extends Controller
{
    public function index()
    {
        $abc = ABC::with([
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
            },
            'abcContent' => function ($q) {
                $q->select(
                    'id',
                    'abc_id',
                    'total_cost'
                );
            },
        ])
            ->get();

        return response()->json($abc);
    }

    public function create()
    {
        //
    }

    public function store(AddABCRequest $request, ABCService $service)
    {
        try {


            $pows = ProgramOfWork::where('b3_project_id', $request['b3_project_id'])->get();

            if (count($pows) == 0)
                return response()->json([
                    'status' => 'Error',
                    'Message' => 'Program of Work is required to generate ABC'
                ], 500);

            if ($request['id'] == null) {

                $service->store($request);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'ABC Successfully Created'
                ]);
            } else {

                $service->update($request);

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'ABC Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function show(int $id)
    {
        try {
            $abc = ABC::with([
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
                },
                'abcContent' => function ($q) {
                    $q->select(
                        'id',
                        'abc_id',
                        'total_cost'
                    );
                },
                'b3Project.dupaPerProjectGroup' => function ($q) {
                    $q->select('id', 'name', 'group_no', 'b3_project_id');
                }
            ])
                ->where('b3_project_id', $id)
                ->first();

            return response()->json($abc);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function edit(ABC $abc)
    {
        //
    }

    public function update(Request $request, ABC $abc)
    {
        //
    }

    public function destroy(ABC $abc)
    {
        try {
            $abc->abcContent()->delete();

            $abc->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'ABC Successfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
