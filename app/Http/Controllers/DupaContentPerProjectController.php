<?php

namespace App\Http\Controllers;

use App\Http\Requests\DupaContentPerProject\AddConsumablePercentageRequest;
use App\Http\Requests\DupaContentPerProject\AddDupaContentPerProjectRequest;
use App\Http\Requests\DupaContentPerProject\AddInputEquipmentAreaRequest;
use App\Http\Requests\DupaContentPerProject\AddInputMaterialAreaRequest;
use App\Http\Requests\DupaContentPerProject\AddMinorToolPercentageRequest;
use App\Http\Requests\DupaPerProject\AddDupaPerProjectRequest;
use App\Models\DupaContentPerProject;
use App\Models\DupaPerProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DupaContentPerProjectController extends Controller
{
    public function index()
    {
        $dupa_content_per_project = DupaContentPerProject::with([
            'dupaEquipmentPerProject' => function ($q) {
                $q->select('id', 'dupa_content_per_project_id', 'equipment_id', 'no_of_unit', 'no_of_hour');
            },
            'dupaLaborPerProject' => function ($q) {
                $q->select('id', 'dupa_content_per_project_id', 'labor_id', 'no_of_person', 'no_of_hour', 'group');
            },
            'dupaMaterialPerProject' => function ($q) {
                $q->select('id', 'dupa_content_per_project_id', 'material_id', 'quantity');
            }
        ])
            ->get();

        return response()->json($dupa_content_per_project);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaContentPerProjectRequest $request)
    {
        try {
            $dupa_content_per_project = DupaContentPerProject::updateOrCreate([
                'dupa_per_project_id' => $request['dupa_per_project_id'],
            ]);

            if ($dupa_content_per_project->wasRecentlyCreated) {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Content Per Project Successfully Added'
                ]);
            } else {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Dupa Content Per Project Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(DupaContentPerProject $dupa_content_per_project)
    {
        $dupa_content = DupaContentPerProject::where('id', $dupa_content_per_project->id)
            ->with([
                'dupaEquipmentPerProject' => function ($q) {
                    $q->select('dupa_content_per_project_id', 'dupa_content_per_projects.consumable_percentage', 'dupa_equipment_per_projects.*', 'equipment.hourly_rate', 'equipment.name', DB::raw('COALESCE(dupa_equipment_per_projects.final_price, round((dupa_equipment_per_projects.no_of_unit * dupa_equipment_per_projects.no_of_hour * equipment.hourly_rate), 2)) as equipment_amount'))
                        ->join('equipment', 'equipment.id', '=', 'dupa_equipment_per_projects.equipment_id')
                        ->join('dupa_content_per_projects', 'dupa_content_per_projects.id', 'dupa_equipment_per_projects.dupa_content_per_project_id');
                },
                'dupaEquipmentPerProject.equipment.equipmentComponent' => function ($q) {
                    $q->select('id', 'equip_id', 'component_name');
                },
                'dupaLaborPerProject' => function ($r) {
                    $r->select('dupa_content_per_project_id', 'dupa_labor_per_projects.*', 'labors.hourly_rate', 'labors.designation', DB::raw('COALESCE(dupa_labor_per_projects.final_price, round((dupa_labor_per_projects.no_of_person * dupa_labor_per_projects.no_of_hour * labors.hourly_rate), 2)) as labor_amount'))
                        ->join('labors', 'labors.id', '=', 'dupa_labor_per_projects.labor_id');
                },
                'dupaMaterialPerProject' => function ($s) {
                    $s->select('dupa_content_per_project_id', 'dupa_material_per_projects.*', DB::raw('round(dupa_material_per_projects.quantity, 2) as quantity'), 'materials.name', 'materials.unit_cost', DB::raw('COALESCE(dupa_material_per_projects.final_price, round((dupa_material_per_projects.quantity * materials.unit_cost), 2)) as material_amount'))
                        ->join('materials', 'materials.id', '=', 'dupa_material_per_projects.material_id');
                }
            ])
            ->first();

        // Get the Output per hour
        $e_output_per_hour = DupaPerProject::find($dupa_content_per_project)
            ->first()
            ->output_per_hour;

        // Get Minor Tool Percentage
        $tool_percentage = DupaContentPerProject::find($dupa_content_per_project)
            ->first()
            ->minor_tool_percentage;

        // Get Consumable Percentage
        $mat_consumable_percentage = DupaContentPerProject::find($dupa_content_per_project)
            ->first()
            ->consumable_percentage;

        // Get Area
        $material_area = DupaContentPerProject::find($dupa_content_per_project)
            ->first()
            ->material_area;

        $equipment_area = DupaContentPerProject::find($dupa_content_per_project)
            ->first()
            ->equipment_area;

        // Get the total sum of dupaLabor
        $a_dupaLabor_Total = round($dupa_content->dupaLaborPerProject->sum('labor_amount'), 2);

        // MINOR TOOL PERCENTAGE
        if ($tool_percentage) {
            $minor_tool_percentage_labor_cost = round(($tool_percentage / 100) * $a_dupaLabor_Total, 2);

            // Get the total sum of dupaEquipment
            $dupaEquipment_Total = round($dupa_content->dupaEquipmentPerProject->sum('equipment_amount'), 2);
            $b_dupaEquipment_Total = round($minor_tool_percentage_labor_cost + $dupaEquipment_Total, 2);
        } else {
            $minor_tool_percentage_labor_cost = null;
            $b_dupaEquipment_Total = round($dupa_content->dupaEquipmentPerProject->sum('equipment_amount'), 2);
        }

        // CONSUMABLE
        if ($mat_consumable_percentage) {
            $consumable_rate = $mat_consumable_percentage / 100;

            // Get Consumable Total
            $consumable_total = round(($dupa_content->dupaMaterialPerProject->sum('material_amount')) * $consumable_rate, 2);

            $f_dupaMaterial_Total = round(($dupa_content->dupaMaterialPerProject->sum('material_amount')) + $consumable_total, 2);
        } else {
            $consumable_total = null;
            // Get the total sum of dupaMaterial
            $f_dupaMaterial_Total = round($dupa_content->dupaMaterialPerProject->sum('material_amount'), 2);
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

        $dupa = $dupa_content->dupaPerProject;
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

    public function edit(DupaContentPerProject $dupa_content_per_project)
    {
        //
    }

    public function update(Request $request, DupaContentPerProject $dupa_content_per_project)
    {
        //
    }

    public function destroy(DupaContentPerProject $dupa_content_per_project)
    {
        try {
            $dupa_content_per_project->delete();

            return response()->json([
                'status' => "Success",
                'message' => "Dupa Content Per Project Deleted Successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function addMinorToolsPercentage(AddMinorToolPercentageRequest $request, DupaPerProject $dupa_per_project)
    {
        try {
            $contents = $dupa_per_project->dupaContentPerProject;

            $contents->minor_tool_percentage = $request['minor_tool_percentage'];

            $contents->update();

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Content Per Project - Minor Tool Percentage Succesfully Added'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function deleteMinorToolsPercentage(DupaPerProject $dupa_per_project)
    {
        try {
            $contents = $dupa_per_project->dupaContentPerProject;

            $contents->minor_tool_percentage = null;

            $contents->save();

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Content Per Project - Minor Tool Percentage Succesfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function addConsumablePercentage(AddConsumablePercentageRequest $request, DupaPerProject $dupa_per_project)
    {

        try {
            $contents = $dupa_per_project->dupaContentPerProject;

            $contents->consumable_percentage = $request['consumable_percentage'];

            $contents->update();

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Content Per Project - Consumable Percentage Succesfully Added'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function deleteConsumablePercentage(DupaPerProject $dupa_per_project)
    {
        try {
            $contents = $dupa_per_project->dupaContentPerProject;

            $contents->consumable_percentage = null;

            $contents->save();

            return response()->json([
                'status' => 'Success',
                'message' => 'Dupa Content Per Project - Consumable Percentage Succesfully Deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function inputMaterialArea(AddInputMaterialAreaRequest $request, DupaPerProject $dupa_per_project)
    {
        $area = $request['area'];

        $content = $dupa_per_project->dupaContentPerProject;
        $content->material_area = $area;
        $content->save();

        return response()->json([
            'status' => "Success",
            'message' => "Dupa Content Per Project - Material Area Succesfully Added"
        ]);
    }

    public function deleteMaterialArea(DupaPerProject $dupa_per_project)
    {
        try {
            $content = $dupa_per_project->dupaContentPerProject;

            if ($content->material_area)
                $content->material_area = null;

            $content->save();

            return response()->json([
                'status' => "Success",
                'message' => "Dupa Content Per Project - Material Area Succesfully Deleted"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function inputEquipmentArea(AddInputEquipmentAreaRequest $request, DupaPerProject $dupa_per_project)
    {
        $area = $request['area'];

        $content = $dupa_per_project->dupaContentPerProject;
        $content->equipment_area = $area;
        $content->save();

        return response()->json([
            'status' => "Success",
            'message' => "Dupa Content Per Project - Equipment Area Succesfully Added"
        ]);
    }

    public function deleteEquipmentArea(DupaPerProject $dupa_per_project)
    {
        try {
            $content = $dupa_per_project->dupaContentPerProject;

            if ($content->equipment_area)
                $content->equipment_area = null;

            $content->save();

            return response()->json([
                'status' => "Success",
                'message' => "Dupa Content Per Project - Equipment Area Succesfully Added"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }
}
