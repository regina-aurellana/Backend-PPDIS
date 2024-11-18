<?php

namespace App\Http\Services\DupaPerProject\Takeoff;

use App\Models\DupaPerProject;
use App\Models\DupaPerProjectGroup;
use App\Models\PowTableContentDupa;
use App\Models\TakeOff;
use App\Models\TakeOffTable;

class TakeOffService
{

    public function store($request)
    {
        $groups = DupaPerProjectGroup::where('b3_project_id', $request['b3_project_id'])->get();
        $takeoff = TakeOff::where('b3_project_id', $request['b3_project_id'])->first();

        $limit = $request['limit'];
        $length = $request['length'];


        if ($takeoff) {
            $limit = $takeoff->limit;
            $length = $takeoff->length;
        }

        foreach ($groups as $key => $group) {

            TakeOff::updateOrCreate(
                [
                    'dupa_per_project_group_id' => $group->id,
                ],
                [
                    'b3_project_id' => $request['b3_project_id'],
                    'limit' => $limit,
                    'length' => $length,
                ]
            );
        }
    }

    public function updatePOWDupaQuantityAndCost($take_off_id)
    {
        //GET TAKE OFF AND ITS DUPA
        $take_off = TakeOff::where('take_offs.id', $take_off_id)->select('b3_project_id', 'table_dupa_components.dupa_id', 'take_off_tables.table_say',)
            ->join('take_off_tables', 'take_off_tables.take_off_id', 'take_offs.id')
            ->join('table_dupa_component_formulas', 'table_dupa_component_formulas.id', 'take_off_tables.table_dupa_component_formula_id')
            ->join('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
            ->join('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
            ->first();

        if ($take_off) {
            //GET DUPA PER PROJECT ID AND USE IT ON FILTERING POW DUPA CONTENT DUPA 
            $dupa_per_project = DupaPerProject::where('dupa_per_projects.b3_project_id', $take_off->b3_project_id)
                ->where('dupa_per_projects.dupa_id', $take_off->dupa_id)
                ->select('dupa_per_projects.id', 'dupa_per_projects.direct_unit_cost')
                ->first();

            $quantity = 0;
            $total_estimated_direct_cost = 0;

            if ($dupa_per_project) {
                $quantity = $take_off->table_say;
                $total_estimated_direct_cost = floatval($dupa_per_project->direct_unit_cost ?? 0) * floatval($quantity);
            }

            // UPDATE THE QUANTITY AND ESTIMATED DIRDCT UNIT COST OF POW TABLE CONTENT DUPA
            PowTableContentDupa::where('dupa_per_project_id', $dupa_per_project->id)->update([
                'quantity' => $quantity,
                'total_estimated_direct_cost' => $total_estimated_direct_cost,
            ]);
        }
    }
}
