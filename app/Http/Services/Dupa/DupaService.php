<?php

namespace App\Http\Services\Dupa;

use App\Models\DupaContent;
use App\Models\DupaPerProject;
use Illuminate\Support\Facades\DB;

class DupaService
{

    public function computeDirectUnitCost(){

        $dupa_contents = DupaContent::with([
            'dupaEquipment' => function($q){
                $q->select('dupa_content_id', 'dupa_equipment.*', 'equipment.hourly_rate', 'equipment.name', DB::raw('(dupa_equipment.no_of_unit * dupa_equipment.no_of_hour * equipment.hourly_rate) as equipment_amount'))
                  ->join('equipment', 'equipment.id', '=', 'dupa_equipment.equipment_id');
            },

            'dupaEquipment.equipment.equipmentComponent',

            'dupaLabor'=> function($r){
               $r->select('dupa_content_id', 'dupa_labors.*', 'labors.hourly_rate', 'labors.designation', DB::raw('(dupa_labors.no_of_person * dupa_labors.no_of_hour * labors.hourly_rate) as labor_amount'))
                  ->join('labors', 'labors.id', '=', 'dupa_labors.labor_id');
            },
            'dupaMaterial' => function($s){
                $s->select('dupa_content_id', 'dupa_materials.*', DB::raw('round(dupa_materials.quantity, 2) as rounded_quantity'), 'materials.unit_cost', DB::raw('(dupa_materials.quantity * materials.unit_cost) as material_amount'))
                ->join('materials', 'materials.id', '=', 'dupa_materials.material_id');
            }
            ])
        ->get();

        $results = [];

        foreach ($dupa_contents as $dupa_content) {
            // Get the Output per hour
        $e_output_per_hour = $dupa_content->dupa->output_per_hour;

        // Get the total sum of dupaLabor
        $a_dupaLabor_Total = round($dupa_content->dupaLabor->sum('labor_amount'), 2);

        // Get the total sum of dupaEquipment
        $b_dupaEquipment_Total = round($dupa_content->dupaEquipment->sum('equipment_amount'), 2);

        // Get the Total sum of Labor and Equipment (A + B)
        $c_total_ab = round($a_dupaLabor_Total + $b_dupaEquipment_Total, 2);

        // Get Direct unit cost (C / D)
        $d_direct_unit_cost_c_d = round($c_total_ab / $e_output_per_hour, 2);

        // Get the total sum of dupaMaterial
        $f_dupaMaterial_Total =round( $dupa_content->dupaMaterial->sum('material_amount'), 2);

        // Get Direct unit cost (E + F)
        $g_direct_unit_cost_e_f = round($d_direct_unit_cost_c_d + $f_dupaMaterial_Total, 2);

        // Get Overhead Contingencies and Miscellaneous(OCM) (9% of G)
        $h_ocm = round(0.09 * $g_direct_unit_cost_e_f, 2);

        // Get Contractor's Profit (8% of G)
        $i_contractors_profit = round(0.08 * $g_direct_unit_cost_e_f, 2);

        // Get Value Added Tax (VAT) 12% of (G + H + I)
        $j_vat = round(0.12 * ($g_direct_unit_cost_e_f + $h_ocm + $i_contractors_profit), 2);

        // Get Total unit Cost (G + H + I + J)
        $k_total_unit_cost = round($g_direct_unit_cost_e_f + $h_ocm + $i_contractors_profit + $j_vat, 2);

        $dupa = $dupa_content->dupa;

        DupaPerProject::whereIn('dupa_id', [$dupa->id])
        ->whereHas('b3Project', function($query) {
            $query->whereNot('status', 'Completed');
        })
        ->update([
            'direct_unit_cost' => $g_direct_unit_cost_e_f
        ]);

        $dupa->direct_unit_cost = $g_direct_unit_cost_e_f;
        $dupa->save();

        }
    }

}