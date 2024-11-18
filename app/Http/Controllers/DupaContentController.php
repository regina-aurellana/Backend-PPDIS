<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DupaContent\AddDupaContentRequest;
use App\Http\Requests\Dupa\MinorToolPercentageRequest;
use App\Http\Requests\DupaContent\AreaRequest;
use App\Models\DupaContent;
use App\Models\Dupa;
use Illuminate\Support\Facades\DB;

class DupaContentController extends Controller
{

    public function index()
    {
        $dupa_content = DupaContent::with(['dupaEquipment', 'dupaLabor', 'dupaMaterial'])
            ->get();

        return response()->json($dupa_content);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaContentRequest $request)
    {
        try {
            DupaContent::updateOrCreate([
                'dupa_id' => $request['dupa_id'],
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Content Successfully Added'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function minorToolsPercentage(MinorToolPercentageRequest $request)
    {

        $dupa = Dupa::where('id', $request->dupa_id)->first();
        
        $percent = $request->minor_tool_percentage;
        $dupa->minor_tool_percentage = $percent;
        $dupa->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Saved'
        ]);
    }

    public function show(DupaContent $content)
    {
        $dupa_content = DupaContent::where('id', $content->id)
            ->with([
                'dupaEquipment' => function ($q) {
                    $q->select('dupa_content_id', 'dupa_contents.consumable_percentage', 'dupa_equipment.*', 'equipment.hourly_rate', 'equipment.name', DB::raw('round((dupa_equipment.no_of_unit * dupa_equipment.no_of_hour * equipment.hourly_rate), 2) as equipment_amount'))
                        ->join('equipment', 'equipment.id', '=', 'dupa_equipment.equipment_id')
                        ->join('dupa_contents', 'dupa_contents.id', 'dupa_equipment.dupa_content_id');
                },
                'dupaEquipment.equipment.equipmentComponent',


                'dupaLabor' => function ($r) {
                    $r->select('dupa_content_id', 'dupa_labors.*', 'labors.hourly_rate', 'labors.designation', DB::raw('round((dupa_labors.no_of_person * dupa_labors.no_of_hour * labors.hourly_rate), 2) as labor_amount'))
                        ->join('labors', 'labors.id', '=', 'dupa_labors.labor_id');
                },
                'dupaMaterial' => function ($s) {
                    $s->select('dupa_content_id', 'dupa_materials.*', DB::raw('round(dupa_materials.quantity, 2) as quantity'), 'materials.name', 'materials.unit_cost', DB::raw('round((dupa_materials.quantity * materials.unit_cost), 2) as material_amount'),)
                        ->join('materials', 'materials.id', '=', 'dupa_materials.material_id');
                },
                // 'dupaMaterial.dupaContent' => function ($t) {
                //     $t->select('id', 'consumable_percentage as consumable_percentage');
                // },
            ])
            // ->select('dupa_contents.consumable_percentage')
            ->first();

        // Get the Output per hour
        $e_output_per_hour = Dupa::find($content)
            ->first()
            ->output_per_hour;

        // Get Minor Tool Percentage
        $tool_percentage = DupaContent::find($content)
            ->first()
            ->minor_tool_percentage;

        // Get Consumable Percentage
        $mat_consumable_percentage = DupaContent::find($content)
            ->first()
            ->consumable_percentage;

        // Get Area
        $material_area = DupaContent::find($content)
            ->first()
            ->material_area;

        $equipment_area = DupaContent::find($content)
            ->first()
            ->equipment_area;

        // Get the total sum of dupaLabor
        $a_dupaLabor_Total = round($dupa_content->dupaLabor->sum('labor_amount'), 2);

        // MINOR TOOL PERCENTAGE
        if ($tool_percentage) {
            $minor_tool_percentage_labor_cost = round(($tool_percentage / 100) * $a_dupaLabor_Total, 2);

            // Get the total sum of dupaEquipment
            $dupaEquipment_Total = round($dupa_content->dupaEquipment->sum('equipment_amount'), 2);
            $b_dupaEquipment_Total = round($minor_tool_percentage_labor_cost + $dupaEquipment_Total, 2);
        } else {
            $minor_tool_percentage_labor_cost = null;
            $b_dupaEquipment_Total = round($dupa_content->dupaEquipment->sum('equipment_amount'), 2);
        }

        // CONSUMABLE
        if ($mat_consumable_percentage) {

            $consumable_rate = $mat_consumable_percentage / 100;

            // Get Consumable Total
            $consumable_total = round(($dupa_content->dupaMaterial->sum('material_amount')) * $consumable_rate, 2);

            $f_dupaMaterial_Total = round(($dupa_content->dupaMaterial->sum('material_amount')) + $consumable_total, 2);
        } else {

            $consumable_total = null;
            // Get the total sum of dupaMaterial
            $f_dupaMaterial_Total = round($dupa_content->dupaMaterial->sum('material_amount'), 2);
        }

        // AREA - DUPA MATERIAL
        if ($material_area) {
            $f_dupaMaterial_Total = round($f_dupaMaterial_Total / $material_area, 2);
        }

        // AREA - DUPA EQUIPMENT
        if ($equipment_area) {
            $b_dupaEquipment_Total = round($b_dupaEquipment_Total / $equipment_area, 2);
        }



        // Get the Total sum of Labor and Equipment (A + B)
        $c_total_ab = round($a_dupaLabor_Total + $b_dupaEquipment_Total, 2);

        // Get Direct unit cost (C / D)
        $d_direct_unit_cost_c_d = round($c_total_ab / $e_output_per_hour, 2);

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
        $dupa->direct_unit_cost = $g_direct_unit_cost_e_f;
        $dupa->update();

        return response()->json([
            'dupa_content' => $dupa_content,
            'a_dupaLaborTotal' => $a_dupaLabor_Total,
            'minor_tool_percentage_labor_cost' => $minor_tool_percentage_labor_cost,
            'mat_consumable_total' => $consumable_total,
            'b_dupaEquipmentTotal' => $b_dupaEquipment_Total,
            'c_total_ab' => $c_total_ab,
            'd_direct_unit_cost_c_d' => $d_direct_unit_cost_c_d,
            'e_output_per_hour' => $e_output_per_hour,
            'f_dupaMaterialTotal' => $f_dupaMaterial_Total,
            'g_direct_unit_cost_e_f' => $g_direct_unit_cost_e_f,
            'h_ocm' => $h_ocm,
            'i_contractors_profit' => $i_contractors_profit,
            'j_vat' => $j_vat,
            'k_total_unit_cost' => $k_total_unit_cost,
        ]);
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(DupaContent $content)
    {
        try {
            $content->delete();

            return response()->json([
                'status' => "Success",
                'message' => "Deleted Successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }


    public function inputEquipmentArea(AreaRequest $request, Dupa $dupaID)
    {

        $area = $request['area'];

        $content = $dupaID->dupaContent;
        $content->equipment_area = $area;
        $content->save();

        info($content);

        return response()->json([
            'status' => "Success",
            'message' => "Area is Saved Successfully"
        ]);
    }


    public function deleteEquipmentArea(Dupa $dupaID)
    {

        try {

            $content = $dupaID->dupaContent;

            if ($content->equipment_area)
                $content->equipment_area = null;

            $content->save();

            return response()->json([
                'status' => "Success",
                'message' => "Deleted Successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }




    public function inputMaterialArea(AreaRequest $request, Dupa $dupaID)
    {

        $area = $request['area'];

        $content = $dupaID->dupaContent;
        $content->material_area = $area;
        $content->save();

        info($content);

        return response()->json([
            'status' => "Success",
            'message' => "Area is Saved Successfully"
        ]);
    }


    public function deleteMaterialArea(Dupa $dupaID)
    {

        try {

            $content = $dupaID->dupaContent;

            if ($content->material_area)
                $content->material_area = null;

            $content->save();

            return response()->json([
                'status' => "Success",
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
