<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\TakeOff;
use App\Models\B3Projects;
use App\Models\TakeOffTable;
use Illuminate\Http\Request;
use App\Models\TableDupaComponent;
use App\Models\DupaPerProjectGroup;
use App\Http\Controllers\Controller;
use App\Models\TableDupaComponentFormula;
use App\Http\Requests\TakeOff\TakeOffRequest;
use App\Http\Services\DupaPerProject\Takeoff\TakeOffService;
use App\Models\TakeOffTableFieldsInput;

class TakeOffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $take_off = B3Projects::with([
            'takeOff.takeOffTable.takeOffTableField' => function ($q) {
                $q->join('unit_of_measurements', 'unit_of_measurements.id', 'take_off_table_fields.measurement_id')
                    ->select('*');
            }
        ])->get();

        return response()->json($take_off);

        // $test = "10+20/5+(10-5)";
        // $result = eval("return $test;");
        // return $result;

    }


    public function create()
    {
    }


    public function store(TakeOffRequest $request)
    {
        try {

            $service = new TakeOffService;

            $service->store($request);

            return response()->json([
                'status' => 'Success',
                'message' => 'Take-Off Created'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show($take_off)
    {
        // $take_off = B3Projects::where('id', $take_off->id)
        // ->with(['takeOff.takeOffTable.takeOffTableField' => function($q){
        //     $q->join('unit_of_measurements', 'unit_of_measurements.id', 'take_off_table_fields.measurement_id')
        //     ->select('*');
        // }
        // ])->get();

        $take_offs = TakeOff::where('take_offs.b3_project_id', $take_off)
            ->join('dupa_per_project_groups', 'dupa_per_project_groups.id', 'take_offs.dupa_per_project_group_id')
            ->select('dupa_per_project_groups.*', 'take_offs.*')
            ->with(
                [
                    'b3Projects' => function ($q) {
                        $q->select(
                            'id',
                            'registry_no',
                            'project_title',
                            'project_nature_id',
                            'project_nature_type_id',
                            'location',
                            'status'
                        );
                        $q->with(['projectNature' => function ($q) {
                            $q->select('id', 'name');
                        }]);

                        $q->with(['projectNatureType' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                    },
                    'takeOffTable' => function ($q) {
                        $q->leftJoin('table_dupa_component_formulas', 'table_dupa_component_formulas.id', 'take_off_tables.table_dupa_component_formula_id')

                            ->leftJoin('sow_categories', 'sow_categories.id', 'take_off_tables.sow_category_id')
                            ->leftJoin('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
                            ->leftJoin('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
                            ->leftJoin('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
                            ->select(
                                'take_off_tables.*',
                                'sow_categories.name as sow_category_name',
                                'table_dupa_components.dupa_id',
                                'table_dupa_components.name',
                                'dupas.description',
                                'dupas.item_number',
                                'table_dupa_component_formulas.formula_id',
                                'formulas.result',
                                'formulas.formula',
                            );

                        $q->with(['takeOffTableField' => function ($q) {

                            $q->with(['measurement' => function ($q) {
                            }]);
                            $q->with(['takeOffTableFieldInput' => function ($q) {
                            }]);
                        }]);

                        $q->with([
                            'takeOffTableFieldsInputDatas' => function ($q) {
                                $q
                                    ->leftJoin('take_off_table_fields', 'take_off_table_fields.id', 'take_off_table_fields_inputs.take_off_table_field_id')
                                    ->leftJoin('take_off_tables', 'take_off_tables.id', 'take_off_table_fields.take_off_table_id')
                                    ->join('marks', 'take_off_tables.id', 'marks.take_off_table_id')
                                    ->leftJoin('unit_of_measurements', 'unit_of_measurements.id', 'take_off_table_fields.measurement_id')
                                    ->select(
                                        'take_off_table_fields_inputs.row_no',
                                        'take_off_table_fields_inputs.id',
                                        'take_off_table_fields_inputs.take_off_table_id',
                                        'take_off_table_fields_inputs.take_off_table_field_id',
                                        'take_off_table_fields.id',
                                        'take_off_table_fields.take_off_table_id',
                                        'take_off_table_fields.measurement_id',
                                        'unit_of_measurements.id',
                                        'unit_of_measurements.name',
                                        'unit_of_measurements.abbreviation',
                                        'take_off_table_fields_inputs.value',
                                        // 'marks.mark_description',


                                    )
                                    ->groupBy(
                                        'take_off_table_fields_inputs.row_no',
                                        'take_off_table_fields_inputs.id',
                                        'take_off_table_fields_inputs.take_off_table_id',
                                        'take_off_table_fields_inputs.take_off_table_field_id',
                                        'take_off_table_fields_inputs.value',
                                        'take_off_table_fields_inputs.deleted_at',
                                        'take_off_table_fields_inputs.created_at',
                                        'take_off_table_fields_inputs.updated_at',
                                        'take_off_table_fields.id',
                                        'take_off_table_fields.take_off_table_id',
                                        'take_off_table_fields.measurement_id',
                                        'unit_of_measurements.id',
                                        'unit_of_measurements.name',
                                        'unit_of_measurements.abbreviation',
                                        // 'marks.mark_description',


                                    );
                            }


                        ]);
                    }
                ]
            )->get();

        // return $take_offs;


        $modifiedTakeOff = $take_offs->map(function ($item) {
            $tableIndex = [];
            foreach ($item->takeOffTable as $key => $table) {
                $table->takeOffTableFieldsInputDatas = $table->takeOffTableFieldsInputDatas->groupBy('row_no')->toArray();
                $tableIndex[] = $key;
            }

            $items = $item->toArray();

            foreach ($tableIndex as $key => $value) {
                unset($items['take_off_table'][$key]['take_off_table_fields_input_datas']);
            }

            return $items;
        });


        $modifiedData = $modifiedTakeOff->toArray();

        $compute = $this->computePerTable($take_off);

        $transformedData2 = [];
        $mergedData = [];

        foreach ($compute as $key => $item) {

            // if(empty($item)){
            //     return $modifiedData;
            // }

            foreach ($item as $tableKey => $takeoff_table) {


                preg_match('/(\d+)/', $tableKey, $matches);
                $tableNumber = $matches[0];


                $transformedData2[$tableNumber] = $takeoff_table;


                $mergedData = [];
                foreach ($modifiedData as $item) {

                    $mergedItem = $item; // Copy the original item

                    foreach ($item['take_off_table'] as $key => $table) {
                        $tableId = $table['id'];

                        if (isset($transformedData2[$tableId])) {
                            // Merge the data for the specific table
                            $mergedItem['take_off_table'][$key]['Result'] = $transformedData2[$tableId];
                        }
                    }

                    // Add the merged item to the result
                    $mergedData[] = $mergedItem;
                }
            }
        }

        if (!$mergedData) {
            return $modifiedData;
        }

        return $mergedData;
    }

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(TakeOff $take_off)
    {
        try {
            $take_off->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Deleted Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Success',
                'message' => $th->getMessage()
            ]);
        }
    }


    public function inputValues($take_off)
    {

        $take_offs = TakeOff::where('id', $take_off)->first();

        $inputValuePerRowAndField = [];
        foreach ($take_offs->takeOffTable as $table) {

            $fieldInput = $table->takeOffTableFieldsInputDatas;
            $fieldInputValue = $fieldInput;

            info($table->takeOffTableFieldsInputDatas);
            // $fieldInputValue[]['take_off_table_id: ' . $table->id] = $fieldInput->groupBy('row_no', 'take_off_table_id');

            foreach ($fieldInputValue as $value) {

                $fields = $value->takeOffTableField;
                foreach ($fields->measurement as $measure) {
                }
            }

            $inputValuePerRowAndField['take_off_table_id: ' . $table->id] = $fieldInputValue->groupBy('row_no');
        }


        return $inputValuePerRowAndField;
    }




    private function computePerTable($take_off)
    {

        // return $b3_project_id;

        $takeoffs = TakeOff::where('b3_project_id', $take_off)->get();

        $result_field_and_value = [];

        foreach ($takeoffs as $key => $takeoff) {

            $field_and_value = [];


            foreach ($takeoff->takeOffTable as $table) {


                $fieldNames = [];
                $fieldValues = [];
                $tableFormulas = [];

                $fields = $table->takeOffTableField;
                $tableID = $table->id;

                $tables_takeoff = TakeOffTable::where('take_off_tables.id', $tableID)
                    ->leftJoin('table_dupa_component_formulas', 'table_dupa_component_formulas.id', 'take_off_tables.table_dupa_component_formula_id')
                    ->leftJoin('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
                    ->leftJoin('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
                    ->leftJoin('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
                    ->select(
                        'take_off_tables.id',
                        'take_off_tables.take_off_id',
                        'take_off_tables.sow_category_id',
                        'take_off_tables.table_dupa_component_formula_id',
                        'take_off_tables.table_say',
                        'table_dupa_components.dupa_id',
                        'table_dupa_components.name',
                        'dupas.description',
                        'dupas.item_number',
                        'table_dupa_component_formulas.formula_id',
                        'formulas.result',
                        'formulas.formula'
                    )
                    ->first();

                $formula = $tables_takeoff->formula;
                $row = [];

                foreach ($fields as $table_field) {

                    $table_fields[] = $table_field;
                    $measurement_name = $table_field->measurement->name;


                    $fieldNames[] = $measurement_name;

                    foreach ($table_field->takeOffTableFieldInput as $key => $table_field) {

                        $rowNo = $table_field->row_no;

                        $column_value = $table_field->value;
                        $fieldValues[$key][] = $column_value;

                        $row[$key][] = $rowNo;
                    }
                }

                $rowNumbers = array_values(array_unique(array_merge(...$row)));

                $field_value = [
                    'fieldName' => $fieldNames,
                    'fieldValue' => $fieldValues
                ];

                $fieldName = $field_value['fieldName'];
                $fieldValue = $field_value['fieldValue'];
                $tableFormula = $formula;

                $results = [];

                if ($formula == "NONE") {

                    $res = null;

                    $field_and_value["table " . $tableID] = [
                        'fieldName' => $fieldNames,
                        'fieldValue' => [],
                        'row_result' => $res,
                        'contingency' => $res,
                        'table_total' => $res,
                        'table_say' => $res
                    ];
                } else {
                    foreach ($fieldValue as $input) {
                        $tableFormulaString = $tableFormula;

                        usort($fieldName, function ($a, $b) {
                            return strlen($b) - strlen($a);
                        });


                        foreach ($fieldName as $nameIndex => $name) {

                            $newFormula = $tableFormulaString;

                            $escapedFieldName = preg_quote($name, '/');

                            $pattern = "/\b$escapedFieldName\b/";

                            // Replace FieldNames with respective input Value
                            $tableFormulaString = preg_replace("/\b$escapedFieldName\b/", $input[$nameIndex], $newFormula);
                        }

                        // Add multiplication operator befor and after ()
                        $tableFormulaString = preg_replace('/([a-zA-Z0-9)])(\()/', '$1*$2', $tableFormulaString);
                        $tableFormulaString = preg_replace('/(\))([a-zA-Z0-9(])/', '$1*$2', $tableFormulaString);

                        // Evaluate the formula using eval() function
                        $result = round(eval("return $tableFormulaString;"), 2);

                        $results[] = $result;
                    }

                    $contingency = $table->contingency / 100;

                    $row_result[] = $results;

                    $table_row_sum = round(array_sum($results), 2);


                    if ($contingency) {

                        $total_contingency = array_sum($results) * $contingency;
                        $table_say = ceil(array_sum($results) + $total_contingency); // Total per table

                        info($total_contingency);
                        info($table_say);
                    } else {
                        $table_say = ceil(array_sum($results));  // Total per table
                    }

                    if ($tables_takeoff->name != null) {


                        $component_formula_id = $tables_takeoff->table_dupa_component_formula_id;

                        $test = TableDupaComponentFormula::where('table_dupa_component_formulas.id', $component_formula_id)
                            ->join('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
                            ->first();

                        $sample = TableDupaComponent::where('dupa_id', $test->dupa_id)
                            ->where('name', '!=', null)->get();


                        if (count($sample) >= 1) {

                            $components_of_dupa = TableDupaComponentFormula::where('table_dupa_components.dupa_id', $test->dupa_id)
                                ->join('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
                                ->where('table_dupa_components.name', null)
                                ->select('table_dupa_component_formulas.id')
                                ->first();


                            $tabless = TakeOffTable::where('table_dupa_component_formula_id', $components_of_dupa->id)
                                ->where('take_off_id', $take_off_table_field_input->id)->first();

                            $field = $tabless->mark;

                            $test_mark = Mark::where('take_off_table_id', $tabless->id)
                                ->where('mark_description', $test->name)->first();

                            info($test->name);

                            TakeOffTableFieldsInput::where('row_no', $test_mark->row_no)->update(['value' => $table_say]);
                        }
                    }

                    $mark = Mark::where('take_off_table_id', $tableID)->get();
                    TakeOffTable::where('id', $tableID)->update(['table_say' => $table_say]);

                    $field_and_value["table " . $tableID] = [
                        'fieldName' => $fieldNames,
                        'fieldValue' => [],
                        'row_result' => $results,
                        'contingency' => $table->contingency,
                        'table_total' => $table_row_sum,
                        'table_say' => $table_say,
                        'mark' => $mark
                    ];

                    for ($i = 0; $i < count($fieldValues); $i++) {
                        $rowNo = $rowNumbers[$i];
                        $field_and_value["table " . $tableID]['fieldValue']['row ' . $rowNo] = $fieldValues[$i];
                    }
                }
            }
            $result_field_and_value["takeOff_id " . $takeoff->id] = $field_and_value;
        }

        //    return $takeoff->id;


        return $result_field_and_value;
    }
}
