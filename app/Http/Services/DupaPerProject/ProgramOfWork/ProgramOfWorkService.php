<?php

namespace App\Http\Services\DupaPerProject\ProgramOfWork;

use App\Models\DupaPerProject;
use App\Models\DupaPerProjectGroup;
use App\Models\PowTable;
use App\Models\PowTableContent;
use App\Models\PowTableContentDupa;
use App\Models\ProgramOfWork;

class ProgramOfWorkService
{

    public function store($request)
    {
        $groups_no = DupaPerProjectGroup::where('b3_project_id', $request['b3_project_id'])->get();
        $program_of_work = ProgramOfWork::where('b3_project_id', $request->b3_project_id)->get();

        foreach ($groups_no as $groups) {

            $grouped[] = $groups->dupaPerProject;
        }

        if (count($program_of_work) == 0) {
            foreach ($grouped as $key => $group) {
                if (count($group) > 0) {
                    $this->save($request, $group, $group[0]->dupa_per_project_group_id);
                }
            }
        } else {
            foreach ($grouped as $key => $group) {

                if (count($group) > 0) {

                    $flag = $program_of_work->where('dupa_per_project_group_id', $group[0]->dupa_per_project_group_id)->first();

                    if (!$flag) {
                        $this->save($request, $group, $group[0]->dupa_per_project_group_id);
                    }
                }
            }
        }
    }

    private function save($request, $group, $group_id)
    {
        try {

            $pow = ProgramOfWork::create([
                'b3_project_id' => $request->b3_project_id,
                'dupa_per_project_group_id' => $group_id,
            ]);

            foreach ($group as $key => $value) {
                // POW TABLE CREATION

                $sow_cat = $group[$key]['sow_category_id'];

                $powTbl_exist = PowTable::where('program_of_work_id', $pow->id)->where('sow_category_id', $sow_cat)->exists();

                if (!$powTbl_exist) {
                    $pow_table = PowTable::create([
                        'program_of_work_id' => $pow->id,
                        'sow_category_id' => $group[$key]['sow_category_id']
                    ]);
                }

                // POW TABLE CONTENT CREATION

                $tblContent = PowTableContent::where('pow_table_id', $pow_table->id)->where('sow_category_id', $group[$key]['sow_category_id'])->where('sow_subcategory_id', $group[$key]['subcategory_id'])->exists();

                if (!$tblContent) {
                    $pow_table_content = PowTableContent::create([
                        'pow_table_id' => $pow_table->id,
                        'sow_category_id' => $group[$key]['sow_category_id'],
                        'sow_subcategory_id' => $group[$key]['subcategory_id'],
                    ]);
                }

                //CHECK IF DUPA HAS ALREADY QUANTITY IN TAKE-OFF (SAY IN TAKE OFF = QUANTITY)
                $checkDupa = $this->checkDupaQuantityOnTakeOff($group[$key]['id'], $request->b3_project_id);
                $quantity = 0;
                $total_estimated_direct_cost = 0;

                if ($checkDupa) {
                    $quantity = $checkDupa->table_say;
                    $total_estimated_direct_cost = floatval($checkDupa->direct_unit_cost ?? 0) * floatval($quantity);
                }

                // POW TABLE CONTENT DUPA CREATION
                PowTableContentDupa::create([
                    'pow_table_content_id' => $pow_table_content->id,
                    'dupa_per_project_id' => $group[$key]['id'],
                    'quantity' => $quantity,
                    'total_estimated_direct_cost' => $total_estimated_direct_cost,
                ]);
            }
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    public function checkDupaQuantityOnTakeOff($dupaPerProjectId, $b3ProjectId)
    {
        $check = DupaPerProject::where('dupa_per_projects.id', $dupaPerProjectId)
            ->join('dupas', 'dupas.id', 'dupa_per_projects.dupa_id')
            ->join('table_dupa_components', 'table_dupa_components.dupa_id', 'dupas.id')
            ->join(
                'table_dupa_component_formulas',
                'table_dupa_component_formulas.table_dupa_component_id',
                'table_dupa_components.id'
            )
            ->join('take_off_tables', 'take_off_tables.table_dupa_component_formula_id', 'table_dupa_component_formulas.id')
            ->join('take_offs', 'take_offs.id', 'take_off_tables.take_off_id')
            ->where('take_offs.b3_project_id', $b3ProjectId)
            ->select('take_off_tables.table_say', 'dupa_per_projects.direct_unit_cost')
            ->first();

        return $check;
    }
}
