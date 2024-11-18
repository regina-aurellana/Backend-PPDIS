<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommunicationCategory;

class CommunicationCategoryController extends Controller
{
    public function index()
    {
       $comms = CommunicationCategory::get();

       return response()->json($comms);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            $comms = CommunicationCategory::updateOrCreate(
                 ['id' => $request['id']],
                 ['description' => $request['description']]
             );


             if ($comms->wasRecentlyCreated) {
                 return response()->json([
                     'status' => 'Created',
                     'message' => 'Successfully Created'
                 ]);
             } else{

                 return response()->json([
                     'status' => 'Updated',
                     'message' => 'Successfully Updated'
                 ]);

             }

            } catch (\Throwable $th) {
             return response()->json([
                 'status' => 'Error',
                 'message' => $th->getMessage()
             ]);
            }
    }

    public function show(CommunicationCategory $comms_category)
    {
        $comms = CommunicationCategory::where('id', $comms_category->id)->first();

        return response()->json($comms);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(CommunicationCategory $comms_category)
    {
        try {
            $comms_category->delete();

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
