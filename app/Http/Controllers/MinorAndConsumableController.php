<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dupa\MinorToolPercentageRequest;
use App\Http\Requests\DupaContent\ConsumableRequest;
use Illuminate\Http\Request;

use App\Models\DupaContent;
use App\Models\Dupa;

class MinorAndConsumableController extends Controller
{
    public function addMinorTool(MinorToolPercentageRequest $request, Dupa $dupaID){

        try {
            $contents = $dupaID->dupaContent;

            $contents->minor_tool_percentage = $request['minor'];

            $contents->update();

            return response()->json([
                'status' => 'Success',
                'message' => 'Minor Tool Percentage is Added'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }

    }

    public function deleteMinorTool(Dupa $dupaID){

            try {
                $contents = $dupaID->dupaContent;

                $contents->minor_tool_percentage = null;

                $contents->save();

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Minor Tool Percentage is Deleted'
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'Error',
                    'message' => $th->getMessage()
                ]);
            }

    }


    public function addConsumable(ConsumableRequest $request, Dupa $dupaID){

        try {
            $contents = $dupaID->dupaContent;

            $contents->consumable_percentage = $request['consumable'];

            $contents->update();

            return response()->json([
                'status' => 'Success',
                'message' => 'Consumable Percentage is Added'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }

    }

    public function deleteConsumable(Dupa $dupaID){

            try {
                $contents = $dupaID->dupaContent;

                $contents->consumable_percentage = null;

                $contents->save();

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Consumable Percentage is Deleted'
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'Error',
                    'message' => $th->getMessage()
                ]);
            }



    }

}
