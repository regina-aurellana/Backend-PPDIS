<?php

namespace App\Http\Controllers;

use App\Http\Requests\DupaPerProjectGroup\AddDupaPerProjectGroupRequest;
use App\Http\Requests\DupaPerProjectGroup\UpdateDupaPerProjectGroupRequest;
use App\Http\Services\DupaPerProject\ProgramOfWork\ProgramOfWorkService;
use App\Http\Services\DupaPerProject\Takeoff\TakeOffService;
use App\Models\DupaPerProject;
use App\Models\DupaPerProjectGroup;
use App\Models\B3Projects;
use App\Models\ProgramOfWork;
use App\Models\TakeOff;
use Illuminate\Http\Request;

class DupaPerProjectGroupController extends Controller
{
    public function index()
    {
        $dupa_per_project_groups = DupaPerProjectGroup::with([
            'dupaPerProject' => function ($q) {
                $q->select('id', 'dupa_id', 'b3_project_id', 'sow_category_id', 'subcategory_id', 'dupa_per_project_group_id', 'item_number', 'description', 'unit_id', 'output_per_hour', 'category_dupa_id', 'direct_unit_cost');
            },
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
                $q->with(['projectNature' => function ($q) {
                    $q->select('id', 'name');
                }]);

                $q->with(['projectNatureType' => function ($q) {
                    $q->select('id', 'name');
                }]);
            },
        ])
            ->get();

        return response()->json($dupa_per_project_groups);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaPerProjectGroupRequest $request)
    {
        try {
            $b3_project_exist = DupaPerProjectGroup::where('b3_project_id', $request['b3_project_id'])->orderBy('created_at', 'asc')->first();

            if (!$b3_project_exist) {
                $group_no = 1;
            } else {
                $latest = DupaPerProjectGroup::where('b3_project_id', $request['b3_project_id'])->select('group_no')->latest()->first();

                $group_no = $latest->group_no + 1;
            }

            $dupa_per_project_group = DupaPerProjectGroup::updateOrCreate(
                ['id' => $request['id']],
                [
                    'b3_project_id' => $request['b3_project_id'],
                    'group_no' => $group_no
                ]
            );

            if ($dupa_per_project_group->wasRecentlyCreated) {

                //CHECK IF TAKEOFF IS ALREADY CREATED
                if (count(TakeOff::where('b3_project_id', $request['b3_project_id'])->get()) > 0) {
                    $service = new TakeOffService;

                    $service->store($request);
                }



                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Per Project Group Succesfully Added'
                ]);
            } else {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Per Project Group Succesfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(B3Projects $dupa_per_project_group)
    {

        $content = B3Projects::where('id', $dupa_per_project_group->id)
            ->with([
                'projectNature' => function ($q) {
                    $q->select('id', 'name');
                },
                'projectNatureType' => function ($q) {
                    $q->select('id', 'name');
                },
                'dupaPerProjectGroup' => function ($q) {
                    $q->with(['dupaPerProject' => function ($q) {
                        $q->select('id', 'dupa_id', 'b3_project_id', 'sow_category_id', 'subcategory_id', 'dupa_per_project_group_id', 'item_number', 'description', 'unit_id', 'output_per_hour', 'category_dupa_id', 'direct_unit_cost');
                    }]);
                },
            ])
            ->get();

        return response()->json($content);
    }

    public function edit(DupaPerProjectGroup $dupa_per_project_group)
    {
        //
    }

    public function update(UpdateDupaPerProjectGroupRequest $request, DupaPerProjectGroup $dupa_per_project_group)
    {
        try {

            $dupa_per_project_group->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Per Project Group Succesfully Updated'
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy(DupaPerProjectGroup $dupa_per_project_group)
    {
        try {
            $dupa_per_project_group->dupaPerProject->each(function ($item) {
                if ($item->dupaContentPerProject) {
                    if ($item->dupaContentPerProject->dupaEquipmentPerProject) {
                        $item->dupaContentPerProject->dupaEquipmentPerProject->each(function ($equipment) {
                            $equipment->delete();
                        });
                    }

                    if ($item->dupaContentPerProject->dupaMaterialPerProject) {
                        $item->dupaContentPerProject->dupaMaterialPerProject->each(function ($material) {
                            $material->delete();
                        });
                    }

                    if ($item->dupaContentPerProject->dupaLaborPerProject) {
                        $item->dupaContentPerProject->dupaLaborPerProject->each(function ($labor) {
                            $labor->delete();
                        });
                    }

                    $item->dupaContentPerProject->delete();
                }

                $item->delete();
            });

            $dupa_per_project_group->delete();

            return response()->json([
                'status' => "Success",
                'message' => "Dupa Per Project Group Successfully Deleted"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }
}
