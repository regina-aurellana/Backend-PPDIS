<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\TakeOff\TakeOffTableRequest;
use App\Http\Requests\TakeOff\TakeOffTableFieldsRequest;
use App\Http\Requests\TakeOff\UpdateTakeOffTableRequest;
use App\Http\Requests\TakeOff\UpdateTakeOffTableFieldsRequest;
use App\Http\Requests\TakeOff\SayTotalRequest;
use App\Models\TakeOff;
use App\Models\TakeOffTable;
use App\Models\TakeOffTableFields;
use App\Models\TakeOffTableFieldsInput;
use App\Models\UnitOfMeasurement;
use App\Models\TableDupaComponent;
use App\Models\TableDupaComponentFormula;
use App\Models\Mark;
use App\Models\DupaPerProject;
use App\Http\Requests\TakeOff\TakeOffTableSelectRequest;



class TakeOffTableController extends Controller
{

    public function index()
    {
        $table = TakeOffTable::join('table_dupa_components', 'table_dupa_components.id', 'take_off_tables.table_dupa_component_id')
            ->join('sow_categories', 'sow_categories.id', 'take_off_tables.sow_category_id')
            ->join('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
            ->join('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
            ->select('table_dupa_components.*', 'table_dupa_components.*', 'take_off_tables.sow_category_id', 'sow_categories.name as sowcat_name', 'table_dupa_component_formulas.table_dupa_component_id', 'table_dupa_component_formulas.formula_id', 'formulas.formula')
            ->get();

        return $table;

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function selectDupaTakeOff(Request $request){
        try {

            // in dropdown to select dupa to add, only show dupa that has assigned formula
            // make a query to filter dupa component with assigned formula


            foreach ($request['dupa_per_project_id'] as $key => $value) {

                $TableDupaComponentFormula = DupaPerProject::join('table_dupa_components', 'dupa_per_projects.dupa_id', '=', 'table_dupa_components.dupa_id')
                   ->join('table_dupa_component_formulas', 'table_dupa_components.id', '=', 'table_dupa_component_formulas.table_dupa_component_id')
                   ->where('dupa_per_projects.id', $request['dupa_per_project_id'][$key])
                   ->where('table_dupa_components.name', null)
                   ->select('table_dupa_component_formulas.id')
                   ->first();

                $request_data[] = [
                    'take_off_id' => $request['take_off_id'],
                    'sow_category_id' => $request['sow_category_id'],
                    'table_dupa_component_formula_id' => $TableDupaComponentFormula->id,
                ];

            }


            foreach ($request['table_dupa_component_formula_id'] as $key => $value) {

                $table_set[] = [
                    'take_off_id' => $request['take_off_id'],
                    'sow_category_id' => $request['sow_category_id'],
                    'table_dupa_component_formula_id' => $request['table_dupa_component_formula_id'][$key],
                ];

            }

            foreach ($table_set as $key => $table) {

                $table_component_id = $table['table_dupa_component_formula_id'];

                $dupa = TableDupaComponent::where('id', $table_component_id)->first();

                $components_of_dupa = TableDupaComponent::where('dupa_id', $dupa->dupa_id)
                ->where('name', "!=", null)
                ->get();

              if($components_of_dupa->isEmpty()){

                $take_off_table =TakeOffTable::insertGetId($table);
                $this->field($take_off_table);


              } else {

                // create fields for each component

                foreach ($components_of_dupa as $comp) {


               $comp_id = TableDupaComponentFormula::where('table_dupa_component_id', $comp->id)->first();

               info($comp_id);

                    $table_comp = [
                        'take_off_id' => $request['take_off_id'],
                        'sow_category_id' => $request['sow_category_id'],
                        'table_dupa_component_formula_id' => $comp_id->id,
                    ];

                    info($table_comp);

                    $hasFormula = TableDupaComponentFormula::where('id', $comp->id)->exists();

                        if($hasFormula){
                            $take_off_table =TakeOffTable::insertGetId($table_comp);
                            $this->field($take_off_table);

                            info('has formula');

                        } else {
                                return response()->json([
                                    'status' => 'Error',
                                    'message' => 'Cannot Proceed to creating Table fields because Formula is not defined. Assign a formula first.'
                                ]);
                            }
                }


                $dupaComp = TableDupaComponent::where('id', $dupa->id)
                ->where('name', null)
                ->first();

                if($dupaComp){

                    // SAVE PARENT TABLE

                        $parent_table =TakeOffTable::insertGetId($table);


                        // CREATE PARENT TABLE FIELDS

                        $field_total = UnitOfMeasurement::where('name', "TOTAL")->first();

                        $field = [
                            'take_off_table_id' => $parent_table,
                            'measurement_id' => $field_total['id'],
                            // 'created_at' => now()
                        ];


                        // SAVE TABLE FIELDS
                        $parent_table_field =TakeOffTableFields::insertGetId($field);


                        // CREATE PARENT TABLE FIELDS DEFAULT ROWS INPUT VALUE

                        $maxRowNo = TakeOffTableFieldsInput::max('row_no');
                        $nextRowNo = $maxRowNo + 1;
                        $default_input_value = 0;


                        $dupa_components = TableDupaComponent::where('dupa_id', $dupa->dupa_id)
                                    ->where('name', "!=", null)
                                    ->get();

                        foreach($dupa_components as $component){

                            $mark = [
                                'take_off_table_id' => $parent_table,
                                'row_no' => $nextRowNo,
                                'mark_description' => $component->name
                            ];


                            $input = [
                                'take_off_table_id' => $parent_table,
                                'row_no' => $nextRowNo,
                                'take_off_table_field_id' => $parent_table_field,
                                'value' => $default_input_value,
                            ];


                            Mark::insert($mark);
                            TakeOffTableFieldsInput::insert($input);


                            $nextRowNo++;

                        }
                    }
            }
          }

          return response()->json([
             'status' => 'Success',
             'message' => 'Take off tables were Created'
            ]);


        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }


    public function store(TakeOffTableRequest $tableRequest)
    {

        try {

            $table = [
                'take_off_id' => $tableRequest['take_off_id'],
                'sow_category_id' => $tableRequest['sow_category_id'],
                'table_dupa_component_id' => $tableRequest['table_dupa_component_id'],
                'created_at' => now()
            ];

            //  Check if table has components

                $dupa = TableDupaComponent::where('id', $tableRequest['table_dupa_component_id'])->first();

                $components_of_dupa = TableDupaComponent::where('dupa_id', $dupa->dupa_id)
                ->where('name', "!=", null)
                ->get();

              if($components_of_dupa->isEmpty()){

                $take_off_table =TakeOffTable::insertGetId($table);
                $this->field($take_off_table);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Take off table is Created'
                ]);

              } else {

                    // create fields for each component
                    foreach ($components_of_dupa as $comp) {

                        $table_comp = [
                            'take_off_id' => $tableRequest['take_off_id'],
                            'sow_category_id' => $tableRequest['sow_category_id'],
                            'table_dupa_component_id' => $comp->id,
                            'created_at' => now()
                        ];

                        $hasFormula = TableDupaComponentFormula::where('table_dupa_component_id', $comp->id)->exists();

                            if($hasFormula){
                                $take_off_table =TakeOffTable::insertGetId($table_comp);
                                $this->field($take_off_table);

                            } else {
                                    return response()->json([
                                        'status' => 'Error',
                                        'message' => 'Cannot Proceed to creating Table fields because Formula is not defined. Assign a formula first.'
                                    ]);
                                }
                    }


                    $dupaComp = TableDupaComponent::where('id', $dupa->id)
                    ->where('name', null)
                    ->first();

                    if($dupaComp){

                        // SAVE PARENT TABLE

                            $parent_table =TakeOffTable::insertGetId($table);


                            // CREATE PARENT TABLE FIELDS

                            $field_total = UnitOfMeasurement::where('name', "TOTAL")->first();

                            $field = [
                                'take_off_table_id' => $parent_table,
                                'measurement_id' => $field_total['id'],
                                // 'created_at' => now()
                            ];


                            // SAVE TABLE FIELDS
                            $parent_table_field =TakeOffTableFields::insertGetId($field);


                            // CREATE PARENT TABLE FIELDS DEFAULT ROWS INPUT VALUE

                            $maxRowNo = TakeOffTableFieldsInput::max('row_no');
                            $nextRowNo = $maxRowNo + 1;
                            $default_input_value = 0;


                            $dupa = TableDupaComponent::where('id', $tableRequest['table_dupa_component_id'])->first();

                            $dupa_components = TableDupaComponent::where('dupa_id', $dupa->dupa_id)
                                        ->where('name', "!=", null)
                                        ->get();

                            foreach($dupa_components as $component){

                                $mark = [
                                    'take_off_table_id' => $parent_table,
                                    'row_no' => $nextRowNo,
                                    'mark_description' => $component->name
                                ];


                                $input = [
                                    'take_off_table_id' => $parent_table,
                                    'row_no' => $nextRowNo,
                                    'take_off_table_field_id' => $parent_table_field,
                                    'value' => $default_input_value,
                                ];


                                Mark::insert($mark);
                                TakeOffTableFieldsInput::insert($input);


                                $nextRowNo++;

                            }

                            return response()->json([
                                'status' => 'Success',
                                'message' => 'Take off table and its associated tables were Created'
                            ]);

                    }


                    return response()->json([
                        'status' => 'Success',
                        'message' => 'Take off table is Created'
                    ]);



              }


        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }


    public function show(TakeOffTable $take_off_table)
    {

        $table = TakeOffTable::where('take_off_tables.id', $take_off_table->id)
            ->join('sow_categories', 'sow_categories.id', 'take_off_tables.sow_category_id')
            ->join('table_dupa_components', 'table_dupa_components.id', 'take_off_tables.table_dupa_component_id')
            ->join('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
            ->join('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
            ->select('table_dupa_components.*', 'table_dupa_components.*', 'take_off_tables.sow_category_id', 'sow_categories.name as sowcat_name', 'table_dupa_component_formulas.table_dupa_component_id', 'table_dupa_component_formulas.formula_id', 'formulas.formula')
            ->first();

        return $table;

    }

    public function takeOffTablebySowCat($take_off_table)
    {

        $table = TakeOffTable::where('take_off_tables.take_off_id', $take_off_table)
            ->join('take_offs', 'take_offs.id', 'take_off_tables.take_off_id')
            ->join('sow_categories', 'sow_categories.id', 'take_off_tables.sow_category_id')
            ->join('table_dupa_components', 'table_dupa_components.id', 'take_off_tables.table_dupa_component_id')
            ->join('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
            ->join('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
            ->join('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
            ->select('table_dupa_components.*', 'dupas.description as dupa_name', 'dupas.item_number', 'take_off_tables.sow_category_id', 'sow_categories.name as sowcat_name', 'table_dupa_component_formulas.table_dupa_component_id', 'table_dupa_component_formulas.formula_id', 'formulas.formula')
            ->get();

        return $table;

    }


    public function edit(string $id)
    {
        //
    }


    public function update(TakeOffTableRequest $tableRequest, TakeOffTable $take_off_table)
    {
        try {

           $take_off_table->update([
                'take_off_id' => $tableRequest['take_off_id'],
                'sow_category_id' => $tableRequest['sow_category_id'],
                'dupa_id' => $tableRequest['dupa_id'],
           ]);


            return response()->json([
                'status' => 'Success',
                'Message' => 'Take-Off Table Updated'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TakeOffTable $take_off_table)
    {
        try {

            $fields = $take_off_table->takeOffTableField;

            foreach ($fields as $field) {
                $field->takeOffTableFieldInput()->delete();
                $field->delete();
            }


            $take_off_table->mark()->delete();
            $take_off_table->delete();

            return response()->json([
                'status' => "Deleted",
                'message' => "Deleted Successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    public function getAllTakeOffTables(TakeOff $take_off_table){

        $tables = TakeOffTable::where('take_off_id', $take_off_table->id)->get();

        foreach($tables as $table){
            $fieldNames = [];
            $fieldValues = [];
            $tableFormulas = [];
            $table_compute = [];
            $rows = [];

            $formula = $table;

            info($formula);

            $fields = $table->takeOffTableField;
            $tableID = $table->id;
            $marks = $table->mark;

            foreach($fields as $table_field){
                $measurement_name = $table_field->measurement->name;
                $fieldNames[] = $measurement_name;

                foreach($table_field->takeOffTableFieldInput as $key => $table_field)
                {
                    $rowNo = $table_field->row_no;

                    if (!isset($rows[$rowNo])) {
                        $rows[$rowNo] = [];
                    }

                    $rows[$rowNo][] = [
                        'table_id' => $table->id,
                        'row_no' => $rowNo,
                        'field_id'=> $table_field->take_off_table_field_id,
                        'field_name' => $measurement_name,
                        'field_value' => $table_field->value
                    ];

                    $fieldValues[$key][] = $table_field->value;

                }
            }

            // Input per row
            $row_inputs = [
                'fieldName' => $fieldNames,
                'fieldValue' => $fieldValues
            ];

            $fieldName = $row_inputs['fieldName'];
            $fieldValue = $row_inputs['fieldValue'];
            $tableFormula = collect($formula)->pluck('formula')->toArray();

            $results = [];

            foreach ($fieldValue as $input)
            {
                $tableFormulaString = $tableFormula[0];

                foreach ($fieldName as $nameIndex => $name) {

                    $tableFormulaString = str_replace($name, $input[$nameIndex], $tableFormulaString);

                }

                // Add multiplication operator where necessary
                $tableFormulaString = preg_replace('/([a-zA-Z0-9)])(\()/', '$1*$2', $tableFormulaString);
                $tableFormulaString = preg_replace('/(\))([a-zA-Z0-9(])/', '$1*$2', $tableFormulaString);

                // Evaluate the formula using eval() function
                $result = eval("return $tableFormulaString;");
                $results[] = $result;
            }

            $table_row_sum = array_sum($results);

            // Compute per row
            $table_compute = [
                'row_inputs' => $rows,
                'row_result' => $results,
                'table_total' => $table_row_sum,
                'take_off_table_id' => $tableID
            ];

            $final_result[] = [
                 $table,
                 $table_compute,
            ];


        }

        return $final_result;

    }

    public function contingency(Request $request, TakeOfftable $take_off_table){

        TakeOfftable::where('id', $take_off_table->id)
        ->update([
            'contingency' => $request['contingency']
        ]);

        return response()->json([
            'status' => 'Success',
            'message' => 'Contingency is saved'
        ]);
    }


    public function saveTakeOffTableField($take_off_table)
    {

        $tables = TakeOffTable::where('id', $take_off_table)
        ->with([
            'dupa' => function($q) {
                $q->leftJoin('unit_of_measurements', 'unit_of_measurements.id', 'dupas.unit_id')
                ->leftJoin('formulas', 'unit_of_measurements.id', 'formulas.unit_of_measurement_id')
                ->select('dupas.id', 'dupas.item_number', 'dupas.description', 'unit_of_measurements.abbreviation as unit_abbreviation', 'formulas.result', 'formulas.formula');
            }
            ])
            ->first();


            $tableID = $tables->id;
            $test = $tables->dupa;
            $formula = $test->formula;

            $components = preg_split('/[+\-*\/]/', $formula, -1, PREG_SPLIT_NO_EMPTY);


            $allMeasures = UnitOfMeasurement::select('name')->get();

            foreach($components as $key => $component){
            $existingMeasure = UnitOfMeasurement::where('name', $component)->get();
            $measure_id = collect($existingMeasure)->pluck('id')->first();

            $field = [
                'take_off_table_id' => $tableID,
                'measurement_id' => $measure_id,
                'created_at' => now()
            ];

            TakeOffTableFields::insert($field);

            }

    }

    public function saveSayTotal(SayTotalRequest $request){

        $table = TakeOfftable::where('id', $request->table_id)->first();

        $table_say_total = $request->table_say_total;

        $table->table_say = $table_say_total;
        $table->save();

    }


    private function field($take_off_table){

        $tables = TakeOffTable::where('take_off_tables.id', $take_off_table)
        ->join('table_dupa_component_formulas', 'table_dupa_component_formulas.id', 'take_off_tables.table_dupa_component_formula_id')

        ->join('table_dupa_components', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
        ->join('dupas', 'dupas.id', 'table_dupa_components.dupa_id')
        ->join('formulas', 'formulas.id', 'table_dupa_component_formulas.formula_id')
        ->select(
            'take_off_tables.id', 'take_off_tables.take_off_id', 'take_off_tables.sow_category_id', 'take_off_tables.table_dupa_component_formula_id', 'take_off_tables.table_say',
            'table_dupa_components.dupa_id', 'table_dupa_components.name',
            'dupas.description', 'dupas.item_number',
            'table_dupa_component_formulas.formula_id',
            'formulas.result', 'formulas.formula')
        ->first();


            $formula = $tables->formula;

            $components = preg_split('/[()+\-*\/\d]/', $formula, -1, PREG_SPLIT_NO_EMPTY);

            foreach($components as $key => $component){
                $existingMeasure = UnitOfMeasurement::where('name', $component)->get();
                $measure_id = collect($existingMeasure)->pluck('id')->first();

                $field = [
                    'take_off_table_id' => $tables->id,
                    'measurement_id' => $measure_id,
                    // 'created_at' => now()
                ];

                $field_exist = TakeOffTableFields::where('take_off_table_id', $tables->id)
                ->where('measurement_id', $measure_id)
                ->exists();

                if($field_exist){
                    continue;
                }

                TakeOffTableFields::insert($field);

                }

    }



}
