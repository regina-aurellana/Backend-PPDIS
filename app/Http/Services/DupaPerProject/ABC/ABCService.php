<?php

namespace App\Http\Services\DupaPerProject\ABC;

use App\Http\Controllers\ABCContentController;
use App\Models\ABC;
use App\Models\ABCContent;
use App\Models\ProgramOfWork;

class ABCService
{

    public function store($request)
    {

        $pows = ProgramOfWork::where('b3_project_id', $request['b3_project_id'])->get();

        foreach ($pows as $pow) {

            $flag = ABC::where('dupa_per_project_group_id', $pow->dupa_per_project_group_id)->first();

            if(!$flag)
            {
                $abc = ABC::create(
                    [
                        'b3_project_id' => $request['b3_project_id'],
                        'dupa_per_project_group_id' => $pow->dupa_per_project_group_id
                    ]
                );
    
                ABCContent::create([
                    'abc_id' => $abc->id,
                    'total_cost' => '0'
                ]);
            }

            // CALL getContent FUNCTION IN ABCCONTENTCONTROLLER TO UPDATE THE GRAND TOTAL COST
            // $abc_content = new ABCContentController;
            // $abc_content->getContent($abc->id);


        }
    }

    public function update($request)
    {
        $pows = ProgramOfWork::where('b3_project_id', $request['b3_project_id'])->get();

        ABC::updateOrCreate(
            ['id' => $request['id']],
            [
                'b3_project_id' => $request['b3_project_id'],
                'dupa_per_project_group_id' => $pows[0]->dupa_per_project_group_id
            ]
        );

        // CALL getContent FUNCTION IN ABCCONTENTCONTROLLER TO UPDATE THE GRAND TOTAL COST
        $abc_content = new ABCContentController;
        $abc_content->getContent($request['id']);
    }

}