<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\TakeOff\TakeOffTableFieldsInputsRequest;
use App\Http\Requests\TakeOff\UpdateTakeOffTableFieldInputRequest;
use App\Http\Requests\Mark\MarkRequest;
use App\Http\Requests\Mark\UpdateMarkRequest;
use App\Models\TakeOffTableFieldsInput;
use App\Models\TakeOffTableFields;
use App\Models\TakeOffTableFormula;
use App\Models\TakeOffTable;
use App\Models\TakeOff;
use App\Models\Mark;
use App\Models\Formula;
use App\Models\TableDupaComponent;
use App\Models\TableDupaComponentFormula;
use App\Models\B3Projects;


class TakeOffTableFieldInputController extends Controller
{

    public function index()
    {
        $take_off_table_input = TakeOffTableFieldsInput::get();

        return response()->json($take_off_table_input);


    }

    public function create()
    {

    }

    public function store(TakeOffTableFieldsInputsRequest $inputRequest, MarkRequest $markRequest)
    {
        try {

            $maxRowNo = TakeOffTableFieldsInput::max('row_no');
            $nextRowNo = $maxRowNo + 1;

            $data = $inputRequest;

            $sets = [];

            // Loop through each set of data
            foreach ($inputRequest['take_off_table_field_id'] as $key => $value) {
                $sets[] = [
                    'take_off_table_id' => $inputRequest['take_off_table_id'],
                    'row_no' => $nextRowNo,
                    'take_off_table_field_id' => $inputRequest['take_off_table_field_id'][$key],
                    'value' => $inputRequest['value'][$key],
                ];
            }

            $mark = [
                'take_off_table_id' => $markRequest['take_off_table_id'],
                'row_no' => $nextRowNo,
                'mark_description' => $markRequest['mark_description'],
            ];

            Mark::insert($mark);
            TakeOffTableFieldsInput::insert($sets);

            return response()->json([
                'status' => "Success",
                'message' => "Inputs Saved",
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function show(TakeOffTable $take_off_table_field_input)
    {
        $fields = $take_off_table_field_input->takeOffTableField;

        $rows = [];

        foreach ($fields as $field) {

            $measurement_name = $field->measurement->name;

            foreach ($field->takeOffTableFieldInput as $input) {
                $rowNo = $input->row_no;

                if (!isset($rows[$rowNo])) {
                    $rows[$rowNo] = [];
                }

                $rows[$rowNo][] = [
                    'field_id' => $field->id,
                    'field_name' => $measurement_name,
                    'field_value' => $input->value
                ];
            }}
         return $rows;
    }

    public function edit(string $id)
    {
        //
    }


    public function update(UpdateTakeOffTableFieldInputRequest $request, MarkRequest $markRequest, TakeOffTable $take_off_table_field_input)
    {
        try {
            $fields = $take_off_table_field_input->takeOffTableField;

            $rows = [];

            // return $fields;

            foreach ($fields as $field) {

                foreach ($field->takeOffTableFieldInput as $input) {

                    $rows[] = [
                        'row_no' => $input->row_no,
                        'take_off_table_field_id' => $input->take_off_table_field_id,
                        'value' => $input->value
                    ];
                }
            }

                $existingValue = collect($rows)->pluck('value')->toArray();
                $newValue = $request->value;

                $input_value = [];

                foreach ($newValue as $key => $value) {
                    $input_value[] = [
                        'row_no' => $request['row_no'],
                        'take_off_table_field_id' => $request['take_off_table_field_id'][$key],
                        'value' => $value,
                        'take_off_table_id' => $markRequest['take_off_table_id'],
                        'created_at' => now()
                    ];
                }

                    $mark_query = Mark::where('row_no', $request->row_no)
                    ->join('take_off_tables', 'take_off_tables.id', 'marks.take_off_table_id')
                    ->update(['mark_description' => $markRequest['mark_description']]);


                    $takeOffTableFieldInputs = TakeOffTableFieldsInput::where('take_off_table_fields_inputs.take_off_table_id', $take_off_table_field_input->id)
                    ->join('take_off_table_fields', 'take_off_table_fields.id', 'take_off_table_fields_inputs.take_off_table_field_id')
                    ->where('take_off_table_fields_inputs.row_no', $request->row_no)
                    ->delete();

                    TakeOffTableFieldsInput::insert($input_value);


                    return response()->json([
                        'status' => "Success",
                        'message' => "Inputs Updated"
                    ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }


    public function destroy($row_no)
    {
        try {
            // $take_off_table_field_input->delete();

           TakeOffTableFieldsInput::where('row_no', $row_no)->delete();
           Mark::where('row_no', $row_no)->delete();

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

    public function inputsByTakeOffIdAndTable(TakeOff $take_off_table_field_input)
    {

        $tables = $take_off_table_field_input->takeOffTable;

        foreach ($tables as  $table) {
            $table_fields = $table->takeOffTableField;

            foreach ($table->takeOffTableField as $table_field) {
                $table_ids = $table_field->take_off_table_id;
                $measurement_name = $table_field->measurement->name;

                    foreach ($table_field->takeOffTableFieldInput as $value) {

                        $table_no[$table_ids][$value->row_no]= [

                                'input_value' => $value->value,
                                'input_field_name' => $measurement_name
                        ];
                }
            }
            $test[] = [
                'table' . $table_ids => $table_no
            ];
        }

         return $test;



    }


    public function computePerTable(B3Projects $b3_project_id){

        // return $b3_project_id;

       $takeoffs = TakeOff::where('b3_project_id', $b3_project_id->id)->get();

       foreach ($takeoffs as $key => $takeoff) {

        $field_and_value =[];
        $formula = [];

        $result_field_and_value = [];

        foreach($takeoff->takeOffTable as $table){


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
                'take_off_tables.id', 'take_off_tables.take_off_id', 'take_off_tables.sow_category_id', 'take_off_tables.table_dupa_component_formula_id', 'take_off_tables.table_say',
                'table_dupa_components.dupa_id', 'table_dupa_components.name',
                'dupas.description', 'dupas.item_number',
                'table_dupa_component_formulas.formula_id',
                'formulas.result', 'formulas.formula')
            ->first();


            $formula = $tables_takeoff->formula;


            $row = [];

            // return $fields;

                foreach($fields as $table_field){
                    $table_fields[] = $table_field;
                    $measurement_name = $table_field->measurement->name;


                    $fieldNames[] = $measurement_name;


                    foreach($table_field->takeOffTableFieldInput as $key => $table_field){

                        $rowNo = $table_field->row_no;

                            $column_value = $table_field->value;
                            $fieldValues[$key][] = $column_value;

                            $row[$key][] = $rowNo;
                    }
                }

                $rowNumbers = array_values(array_unique(array_merge(...$row)));

                info($rowNumbers);

                    $field_value = [
                    'fieldName' => $fieldNames,
                    'fieldValue' => $fieldValues
                ];

                $fieldName = $field_value['fieldName'];
                $fieldValue = $field_value['fieldValue'];
                $tableFormula = $formula;

                $results = [];

                if($formula == "NONE"){

                    $res = null;

                    $field_and_value["table " . $tableID] = [
                        'fieldName' => $fieldNames,
                        'fieldValue' => [],
                        'row_result' => $res,
                        'contingency' => $res,
                        'table_total' => $res,
                        'table_say' => $res
                    ];


                } else{
                    foreach ($fieldValue as $input)
                {
                    $tableFormulaString = $tableFormula;

                    usort($fieldName, function ($a, $b) {
                        return strlen($b) - strlen($a);
                    });

                    info($fieldName);


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
                    $result = eval("return $tableFormulaString;");

                        $results[] = $result;
                }

                $contingency = $table->contingency / 100;

                $row_result[] = $results;

                $table_row_sum = array_sum($results);


                if($contingency){

                    info('with contingency din dapat');
                    $total_contingency = array_sum($results) * $contingency;
                    $table_say = array_sum($results) + $total_contingency; // Total per table

                    info($total_contingency);
                    info($table_say);

                } else {
                    $table_say = array_sum($results);  // Total per table
                }

                if($tables_takeoff->name != null){


                    $component_formula_id = $tables_takeoff->table_dupa_component_formula_id;

                    $test = TableDupaComponentFormula::where('table_dupa_component_formulas.id', $component_formula_id)
                    ->join('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
                    ->first();

                    $sample = TableDupaComponent::where('dupa_id', $test->dupa_id)
                    ->where('name', '!=', null)->get();


                    if(count($sample)>=1){

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

                            TakeOffTableFieldsInput::where('row_no', $test_mark->row_no)->update(['value' => $table_say]);
                    }
                }

                $field_and_value["table " . $tableID] = [
                    'fieldName' => $fieldNames,
                    'fieldValue' => [],
                    'row_result' => $results,
                    'contingency' => $table->contingency,
                    'table_total' => $table_row_sum,
                    'table_say' => $table_say
                ];

                for ($i = 0; $i < count($fieldValues); $i++) {
                    $rowNo = $rowNumbers[$i];
                    $field_and_value["table " . $tableID]['fieldValue']['row' . $rowNo] = $fieldValues[$i];
                }
                }

        }

        $result_field_and_value[] = $field_and_value;

       }


        return $result_field_and_value;



    }









}
