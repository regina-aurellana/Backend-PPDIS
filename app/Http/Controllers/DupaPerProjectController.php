<?php

namespace App\Http\Controllers;

use App\Http\Requests\DupaPerProject\AddDupaPerProjectRequest;
use App\Http\Services\DupaPerProject\ABC\ABCService;
use App\Http\Services\DupaPerProject\ProgramOfWork\ProgramOfWorkService;
use App\Http\Services\DupaPerProject\WorkSchedule\WorkScheduleService;
use App\Models\Dupa;
use App\Models\DupaContentPerProject;
use App\Models\DupaEquipmentPerProject;
use App\Models\DupaLaborPerProject;
use App\Models\DupaMaterialPerProject;
use App\Models\DupaPerProject;
use App\Models\ProgramOfWork;
use App\Models\TakeOff;
use App\Models\B3Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\DupaPerProject\LOME\LOMEService;
use App\Http\Services\DupaPerProject\MER\MERService;

class DupaPerProjectController extends Controller
{
    public function index()
    {
        $this->computeDirectUnitCost();

        $dupa_per_project = DupaPerProject::join('category_dupas', 'category_dupas.id', 'dupa_per_projects.category_dupa_id')
            ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupa_per_projects.unit_id')
            ->join('sow_sub_categories', 'sow_sub_categories.id', 'dupa_per_projects.subcategory_id')
            ->select('dupa_per_projects.id', 'dupa_per_projects.item_number', 'sow_sub_categories.name as scope_of_work_subcategory', 'dupa_per_projects.description', 'unit_of_measurements.abbreviation', 'dupa_per_projects.direct_unit_cost', 'category_dupas.name as dupa_category')
            ->orderBy('dupa_per_projects.id')
            ->paginate(5000);

        return response()->json($dupa_per_project);
    }

    public function create()
    {
        //
    }

    public function store(AddDupaPerProjectRequest $request)
    {
        try {
            // QUERY THE EXISTING DUPA WITH ONLY NEEDED COLUMNS TO ENSURE THE RESPONSIVENESS
            $dupas_exist = Dupa::with([
                'dupaContent' => function ($q) {
                    $q->select('id', 'dupa_id', 'minor_tool_percentage', 'consumable_percentage', 'material_area', 'equipment_area');
                },
                'dupaContent.dupaEquipment' => function ($q) {
                    $q->select('dupa_content_id', 'equipment_id', 'no_of_unit', 'no_of_hour');
                },
                'dupaContent.dupaMaterial' => function ($q) {
                    $q->select('dupa_content_id', 'material_id', 'quantity');
                },
                'dupaContent.dupaLabor' => function ($q) {
                    $q->select('dupa_content_id', 'labor_id', 'no_of_person', 'no_of_hour', 'group');
                }
            ])
                ->whereIn('id', $request['dupas'])
                ->get();


            // check if b3project has existing group, if none, make default group 1



            // IF THE QUERY DUPA EXISTED, PROCEED TO CREATION OF ENTRIES
            if ($dupas_exist) {
                // LOOP THROUGH THE DUPA ARRAY EVEN IF ITS SINGLE DUPA
                foreach ($dupas_exist as $key => $dupa_exist) {
                    // SET UP THE VARIABLES TO GET THE LOADED RELATED TABLES
                    $dupa_content = $dupa_exist->dupaContent;

                    $dupa_equipment = $dupa_content->dupaEquipment ?? '';
                    $dupa_material = $dupa_content->dupaMaterial ?? '';
                    $dupa_labor = $dupa_content->dupaLabor ?? '';


                    // If group exist


                    // CREATE DUPA PER PROJECT ALONG WITH DUPA CONTENT PER PROJECT
                    $dupa_create = DupaPerProject::create([
                        'dupa_id' => $request['dupas'][$key],
                        'b3_project_id' => $request['b3_project_id'],
                        'sow_category_id' => $request['sow_category_id'],
                        'subcategory_id' => $dupa_exist->subcategory_id,
                        'dupa_per_project_group_id' => $request['dupa_per_project_group_id'],
                        'item_number' => $dupa_exist->item_number,
                        'description' => $dupa_exist->description,
                        'unit_id' => $dupa_exist->unit_id,
                        'output_per_hour' => $dupa_exist->output_per_hour,
                        'direct_unit_cost' => $dupa_exist->direct_unit_cost,
                        'category_dupa_id' => $dupa_exist->category_dupa_id,
                    ]);

                    $dupa_content_create = DupaContentPerProject::create([
                        'dupa_per_project_id' => $dupa_create->id,
                        'minor_tool_percentage' => $dupa_content->minor_tool_percentage ?? '',
                        'consumable_percentage' => $dupa_content->consumable_percentage ?? '',
                        'material_area' => $dupa_content->material_area ?? '',
                        'equipment_area' => $dupa_content->equipment_area ?? '',
                    ]);

                    // LOOPING THROUGH THE FOUND DUPA WITH ITS RELATIONSHIPS. IF THE EAGERLOADED RELATED TABLES ARE NULL, IT WILL NOT CREATE ANYTHING.
                    if ($dupa_equipment) {
                        $data = [];
                        $mer_service = new MERService;
                        foreach ($dupa_equipment as $equipment) {
                            DupaEquipmentPerProject::create([
                                'dupa_content_per_project_id' => $dupa_content_create->id,
                                'equipment_id' => $equipment->equipment_id,
                                'no_of_unit' => $equipment->no_of_unit,
                                'no_of_hour' => $equipment->no_of_hour
                            ]);

                            $data[] = [
                                'equipment_id' => $equipment->equipment_id,
                            ];
                        }
                        $mer_service->MERFromDupa($data, $request['b3_project_id']);
                    }

                    if ($dupa_material) {
                        // $data = [];
                        foreach ($dupa_material as $material) {
                            DupaMaterialPerProject::create([
                                'dupa_content_per_project_id' => $dupa_content_create->id,
                                'material_id' => $material->material_id,
                                'quantity' => $material->quantity
                            ]);

                            // $data[] = [
                            //     'material_id' => $material->material_id,
                            //     'quantity' => $material->quantity
                            // ];
                        }
                        // DEPRECATED ** FOR POSTERITY
                        $lome_service = new LOMEService;
                        $lome_service->LomeFromDupa($request['b3_project_id']);
                    }


                    if ($dupa_labor)
                        foreach ($dupa_labor as $labor) {
                            DupaLaborPerProject::create([
                                'dupa_content_per_project_id' => $dupa_content_create->id,
                                'labor_id' => $labor->labor_id,
                                'no_of_person' => $labor->no_of_person,
                                'no_of_hour' => $labor->no_of_hour,
                                'group' => $labor->group ?? ''
                            ]);
                        }
                }

                //CHECK IF POW IS ALREADY CREATED
                if (count(ProgramOfWork::where('b3_project_id', $request['b3_project_id'])->get()) > 0) {
                    $service = new ProgramOfWorkService;
                    $service->store($request);

                    //UPDATE ABC IF POW IS UPDATED
                    $abc_service = new ABCService;
                    $abc_service->store($request);

                    //UPDATE WORKSCHEDULE IF POW IS UPDATED
                    $workschedule_service = new WorkScheduleService;
                    $workschedule_service->store($request);
                }

                return response()->json([
                    'status' => "Created",
                    'message' => "Dupa Per Project Successfully Created"
                ]);
            } else {
                // IF THE DUPA DOES NOT EXIST, THROW A 404 ERROR
                return response()->json([
                    'status' => 'Not Found',
                    'message' => 'Dupa with the specified dupa_id not found'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(DupaPerProject $dupa_per_project)
    {
        $content = DupaPerProject::where('dupa_per_projects.id', $dupa_per_project->id)
            ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupa_per_projects.unit_id')
            ->join('category_dupas', 'category_dupas.id', 'dupa_per_projects.category_dupa_id')
            ->join('sow_sub_categories', 'sow_sub_categories.id', 'dupa_per_projects.subcategory_id')
            ->join('dupa_content_per_projects', 'dupa_per_projects.id', 'dupa_content_per_projects.dupa_per_project_id')
            ->select('dupa_per_projects.*', 'unit_of_measurements.abbreviation', 'category_dupas.name as dupa_category', 'sow_sub_categories.name as sow_subcategory', 'dupa_content_per_projects.id as dupa_content_per_project_id')
            ->first();

        return response()->json($content);
    }


    public function dupaListForTakeoffSelect($b3_project_id, $take_off_id)
    {

        // Get b3 project's dupa per project groups and its corresponding dupa
        $content = B3Projects::where('id', $b3_project_id)
            ->with([
                'dupaPerProjectGroup' => function ($q) {
                    $q->with(['dupaPerProject' => function ($q) {
                        $q->select('id as dupa_per_project_id', 'b3_project_id', 'sow_category_id', 'subcategory_id', 'dupa_per_project_group_id', 'item_number', 'description', 'unit_id', 'output_per_hour', 'category_dupa_id', 'direct_unit_cost');
                    }]);
                },
            ])
            ->first();


        foreach ($content->dupaPerProjectGroup as $key => $group) {

            $grouped_dupa_per_project[] = $group->dupaPerProject;
        }

        // Get b3 Project's list of take-offs
        $take_offs = TakeOff::where('b3_project_id', $b3_project_id)->get();

        // Combine Take-off and Dupa per project
        foreach ($take_offs as $key => $take_off) {
            $res[] = [
                'take_off_id' => $take_off->id,
                'group' => $grouped_dupa_per_project[$key],
            ];
        }

        // Filter result by take_off_id
        $filteredRes = array_values(array_filter($res, function ($item) use ($take_off_id) {
            return $item['take_off_id'] == $take_off_id;
        }));

        return $filteredRes;
    }

    public function showByProjectID($b3_project_id)
    {
        $contents = DupaPerProject::where('b3_project_id', $b3_project_id)->get();

        $group_by = $contents->groupBy('dupa_per_project_group_id');

        $results = $group_by->map(function ($group) {
            return $group->map(function ($item) {
                return [
                    'id' => $item->id,
                    'dupa_id' => $item->dupa_id,
                    'b3_project_id' => $item->b3_project_id,
                    'sow_category_id' => $item->sow_category_id,
                    'subcategory_id' => $item->subcategory_id,
                    'dupa_per_project_group_id' => $item->dupa_per_project_group_id,
                    'item_number' => $item->item_number,
                    'description' => $item->description,
                    'unit_id' => $item->unit_id,
                    'output_per_hour' => $item->output_per_hour,
                    'category_dupa_id' => $item->category_dupa_id,
                    'direct_unit_cost' => $item->direct_unit_cost,
                ];
            });
        });

        return response()->json($results);
    }


    public function dupaByProjectID($b3_project_id)
    {
        $content = DupaPerProject::where('dupa_per_projects.b3_project_id', $b3_project_id)
            ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupa_per_projects.unit_id')
            ->join('category_dupas', 'category_dupas.id', 'dupa_per_projects.category_dupa_id')
            ->join('sow_sub_categories', 'sow_sub_categories.id', 'dupa_per_projects.subcategory_id')
            ->select('dupa_per_projects.*', 'unit_of_measurements.abbreviation', 'category_dupas.name as dupa_category', 'sow_sub_categories.name as sow_subcategory')
            ->get();

        return response()->json($content);
    }

    public function edit(DupaPerProject $dupa_per_project)
    {
        //
    }

    public function update(Request $request, DupaPerProject $dupa_per_project)
    {
        //
    }

    public function destroy(DupaPerProject $dupa_per_project)
    {
        try {

            foreach ($dupa_per_project->dupaContentPerProject->dupaEquipmentPerProject as $dupa_equip) {
                $dupa_equip->delete();
            }

            foreach ($dupa_per_project->dupaContentPerProject->dupaLaborPerProject as $dupa_labor) {
                $dupa_labor->delete();
            }

            foreach ($dupa_per_project->dupaContentPerProject->dupaMaterialPerProject as $dupa_mat) {
                $dupa_mat->delete();
            }

            $dupa_equip = $dupa_per_project->dupaContentPerProject->delete();

            $dupa_per_project->delete();

            return response()->json([
                'status' => "Success",
                'message' => "Dupa Per Project Successfully Deleted"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    private function computeDirectUnitCost()
    {
        $dupa_contents = DupaContentPerProject::with([
            'dupaEquipmentPerProject' => function ($q) {
                $q->select('dupa_content_per_project_id', 'dupa_equipment_per_projects.*', 'equipment.hourly_rate', 'equipment.name', DB::raw('(dupa_equipment_per_projects.no_of_unit * dupa_equipment_per_projects.no_of_hour * equipment.hourly_rate) as equipment_amount'))
                    ->join('equipment', 'equipment.id', '=', 'dupa_equipment_per_projects.equipment_id');
            },
            'dupaEquipmentPerProject.equipment.equipmentComponent' => function ($q) {
                $q->select('id', 'equip_id', 'component_name');
            },
            'dupaLaborPerProject' => function ($q) {
                $q->select('dupa_content_per_project_id', 'dupa_labor_per_projects.*', 'labors.hourly_rate', 'labors.designation', DB::raw('(dupa_labor_per_projects.no_of_person * dupa_labor_per_projects.no_of_hour * labors.hourly_rate) as labor_amount'))
                    ->join('labors', 'labors.id', '=', 'dupa_labor_per_projects.labor_id');
            },
            'dupaMaterialPerProject' => function ($q) {
                $q->select('dupa_content_per_project_id', 'dupa_material_per_projects.*', DB::raw('round(dupa_material_per_projects.quantity, 2) as rounded_quantity'), 'materials.unit_cost', DB::raw('(dupa_material_per_projects.quantity * materials.unit_cost) as material_amount'))
                    ->join('materials', 'materials.id', '=', 'dupa_material_per_projects.material_id');
            }
        ])
            ->get();

        foreach ($dupa_contents as $dupa_content) {
            // Get the Output per hour
            $e_output_per_hour = $dupa_content->dupaPerProject->output_per_hour;

            // Get the total sum of dupaLabor
            $a_dupaLabor_Total = round($dupa_content->dupaLaborPerProject->sum('labor_amount'), 2);

            // Get the total sum of dupaEquipment
            $b_dupaEquipment_Total = round($dupa_content->dupaEquipmentPerProject->sum('equipment_amount'), 2);

            // Get the Total sum of Labor and Equipment (A + B)
            $c_total_ab = round($a_dupaLabor_Total + $b_dupaEquipment_Total, 2);

            // Get Direct unit cost (C / D)
            $d_direct_unit_cost_c_d = round($c_total_ab / $e_output_per_hour, 2);

            // Get the total sum of dupaMaterial
            $f_dupaMaterial_Total = round($dupa_content->dupaMaterialPerProject->sum('material_amount'), 2);

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
            $dupa->save();
        }
    }
}
