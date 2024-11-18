<?php

namespace App\Http\Controllers\B3Project;

use App\Enums\B3ProjectStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\B3Projects;
use Illuminate\Http\Request;

class SendForApprovalController extends Controller
{

    public function __invoke(B3Projects $b3_project)
    {
        try {
            
            $b3_project->update([
                'status' => B3ProjectStatusEnum::FOR_APPROVAL->value
            ]);

            return response([
                'message' => 'B3 Project sent for approval',
                'status' => 'success'
            ], 200);

        } catch (\Throwable $th) {
            info('B3 Project for approval error: ' . $th->getMessage());
            return response([
                'message' => $th->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }    

}
