<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SowCategory\SowCategoryRequest;

use App\Models\SowCategory;

class SowCategoryController extends Controller
{

    public function index()
    {
        $sow_cat = SowCategory::get();

        return response()->json($sow_cat);
    }


    public function create()
    {
        //
    }

    public function store(SowCategoryRequest $request)
    {
        try {

            if($request->name){
                $sow_req = $request->name;

                //CHECK IF $request->name EXISTS
                $sow_req_exists = SowCategory::withTrashed()->where('name', $sow_req)->get();


                if(!$sow_req_exists->isEmpty()){

                    $hasDuplicates = false;
                    $hasDeleted = false;

                    foreach($sow_req_exists->pluck('deleted_at') as $sow_req_exist)

                    info($sow_req_exist);

                        if(!$sow_req_exist){
                            $hasDuplicates = true;
                                $hasDeleted = false;

                        } else {
                            $hasDuplicates = true;
                                $hasDeleted = true;
                        }

                        //RETURN ERROR IF HAS DUPLICATE
                        if($hasDuplicates){

                            if($hasDeleted){

                                foreach($sow_req_exists as $data_to_restore)
                                  $data_to_restore->restore();

                            return response()->json([
                                'status' => "Restored",
                                'message' => "We've found the same category name from the database and restored it for you."
                            ]);

                            } else {

                             $name = $sow_req_exists->pluck('name')->toArray();

                                return response()->json([
                                    'status' => "Exist",
                                    'message' => $name[0] . ' already exists.'
                                ]);

                            }
                        }

                }

                if( !in_array( $sow_req, $sow_req_exists->pluck('name')->toArray() ))
                {
                    $sow_cat = SowCategory::updateOrCreate(
                                ['id' => $request['id']],
                                [
                                    'item_code' => $request->item_code,
                                    'name' => $sow_req,
                                ]
                        );

                        if ($sow_cat->wasRecentlyCreated) {
                                    return response()->json([
                                        'status' => "Created",
                                        'message' => "SOW Successfully Created"
                                    ]);
                                }else{
                                    return response()->json([
                                        'status' => "Updated",
                                        'message' => "SOW Successfully Updated "
                                    ]);
                                }
                }

            }


        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(SowCategory $sowcat)
    {
        $sow_cat = SowCategory::where('sow_categories.id', $sowcat->id)
        ->first();

        return response()->json($sow_cat);
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(SowCategory $sowcat)
    {
        try {
            $sowcat->delete();

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
