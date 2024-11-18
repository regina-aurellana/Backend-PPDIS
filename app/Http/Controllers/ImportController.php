<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

use App\Models\SowCategory;
use App\Models\SowSubCategory;
use App\Models\SowReference;
use App\Models\UnitOfMeasurement;
use App\Models\DupaContent;
use App\Models\Dupa;
use App\Models\DupaLabor;
use App\Models\Labor;
use App\Models\DupaMaterial;
use App\Models\DupaMaterialNotes;
use App\Models\Material;
use App\Models\DupaEquipment;
use App\Models\DupaEquipmentNotes;
use App\Models\Equipment;

use App\Models\TakeOffTable;
use App\Models\TakeOffTableFields;
use App\Models\TakeOffTableFieldsInput;
use App\Models\Mark;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function upload(Request $request) {

         try {

             if ($request->hasFile('filepond')) {

             $file = $request->file('filepond');
             $folder = uniqid(). '-' .now()->timestamp;
             $filename = $file->getClientOriginalName();

             $file->storeAs('temp/'.$folder, $filename);

             TemporaryFile::create([
                 'filename' => $filename,
                 'folder' => $folder
             ]);

             return $folder;
             }

             return '';


         } catch (\Throwable $th) {
             info($th->getMessage());
         }
    }

    public function revert(){

        $folder = request()->getContent();

        TemporaryFile::where('folder', $folder)->delete();
        Storage::deleteDirectory('temp/'.$folder);
        return '';
    }

    public function importSubcatFirstLvl(Request $request){

    try {

        $file = $request->file('filepond');
        $folder = uniqid(). '-' .now()->timestamp;

        $filepath = $file->store('temp');

        $rows = SimpleExcelReader::create(storage_path('app/'.$filepath))->getRows();
        $collection = collect($rows->toArray());

        $sow_cats = $collection->pluck('Sow Cat Name');
        $parent_item_codes = $collection->pluck('Parent Sub Category Item_code');
        $item_code = $collection->pluck('Item Code');
        $description = $collection->pluck('Description');

        foreach ($sow_cats as $sow_cat) {
            $sow_cats_id[] = SowCategory::where('name', $sow_cat)->select('id')->first();
        }

        $sowcat_id = collect($sow_cats_id)->pluck('id')->toArray();

        for($sowcat = 0; $sowcat < count($item_code); $sowcat++){

            $item_codes [] = $item_code[$sowcat];
            $data [] = [
                'sow_category_id' => $sowcat_id[$sowcat],
                'item_code' => $item_code[$sowcat],
                'name' => $description[$sowcat]
            ];
        }

        $existing_item_code = SowSubCategory::select('item_code')->whereIn('item_code', $item_codes)->pluck('item_code');

        foreach ($data as $insert) {
            if (in_array($insert['item_code'], $existing_item_code->toArray())) {
                SowSubCategory::where('item_code', $insert['item_code'])->update([
                    'sow_category_id' => $insert['sow_category_id'],
                    'item_code' => $insert['item_code'],
                    'name' => $insert['name'],
                ]);

            }else{
                $data_to_insert[] = [
                    'sow_category_id' => $insert['sow_category_id'],
                    'item_code' => $insert['item_code'],
                    'name' => $insert['name'],
                    'created_at' => now(),
                ];
            }
        }

        $insertedIds = [];
        $parent_subcat_ids = [];


        foreach (array_chunk($data_to_insert, 1000) as $data) {
            $parent_subcat_ids = [];

            foreach ($data as $row) {
                $parent_subcat_ids = [];
                $insertedIds [] = SowSubCategory::insertGetId($row);
            }

        }

    foreach ($parent_item_codes as $parent_item_code) {
        $parent_subcat_ids[] = SowSubCategory::where('item_code', $parent_item_code)->select('id')->first();
    }

    $parent_subcat_id = collect($parent_subcat_ids)->pluck('id')->toArray();

        foreach ($insertedIds as $key => $insertedId) {
            SowReference::updateOrCreate(
                ['sow_sub_category_id' => $insertedId,
                'parent_sow_sub_category_id' => $parent_subcat_id[$key]],
            );

        }

        return response()->json([
            'status' => "Success",
            'message' => "Import Successful"
        ]);


    } catch (\Throwable $th) {
        return response()->json([
            'status' => "Error",
            'message' => $th->getMessage()
        ]);
    }

    }

    private function getDupaDetailLineValue($collection, $index)
    {

        for($x=3; $x<=5; $x++)
        {
            if($collection[$index][$x] != ':' && !empty($collection[$index][$x]))
            {
                return [
                    'value' => $collection[$index][$x],
                    'index' => $x
                ];                
            }
        }

        return [];
    }

    private function getStringValue($item)
    {
        if ($item instanceof DateTimeImmutable) {
            $item = $item->format('Y-m-d H:i:s');
        }
        
        if (is_string($item)) {
            return preg_replace('/[^a-zA-Z0-9]/', '', Str::lower($item));
        }
        
        return $item;
    }

    public function importDupa(Request $request){

        try {

            $file = $request->file('filepond');
            $folder = uniqid(). '-' .now()->timestamp;

            $filepath = $file->store('temp');

            $row = SimpleExcelReader::create(storage_path('app/'.$filepath))->noHeaderRow();
            
            $transaction = DB::transaction(function () use($row, $request) {

                $rows = $row->getRows();
                $collection = collect($rows->toArray());

                $itemNumberDescription = 'itemnodescription';
                $unitOfMeasurement = 'unitofmeasurement';
                $outputPerHourAsSubmitted = 'outputperhourassubmitted';
                $laborWordToSearchStart = 'designation';
                $laborWordToSearchEnd = 'subtotalfora1assubmitted';
                $equipmentWordToSearchStart = 'nameandcapacity';
                $equipmentWordToSearch2Start = 'namecapacity';
                $equipmentWordToSearchEnd = 'subtotalforb1assubmitted';
                $materialWordToSearchStart = 'nameandspecification';
                $materialWordToSearch2Start = 'namespecification';
                $materialWordToSearchEnd = 'subtotalforf1assubmitted';

                $itemNumberDescriptionIndex = null;
                $unitOfMeasurementIndex = null;
                $outputPerHourAsSubmittedIndex = null;
                $laborRowStart = null;
                $laborRowEnd = null;
                $equipmentRowStart = null;
                $equipmentRowEnd = null;
                $materialRowStart = null;
                $materialRowEnd = null;
                
                $index = 0;
                $isDone = false;

                foreach ($collection as $line) {
                    $cleanedLine = array_map(function($item) {
                        return preg_replace('/[^a-zA-Z0-9]/', '', Str::lower($this->getStringValue($item)));
                    }, $line);
                    
                    // GET INDEX FOR PROJECT INFO
                    if (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $itemNumberDescription), $cleanedLine)) {
                        $itemNumberDescriptionIndex = $index;
                    }
                    
                    // GET INDEX FOR UNIT OF MEASUREMENT
                    if (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $unitOfMeasurement), $cleanedLine)) {
                        $unitOfMeasurementIndex = $index;
                    }
                    
                    // GET INDEX FOR OUTPUT PER HOUR
                    if (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $outputPerHourAsSubmitted), $cleanedLine) && $outputPerHourAsSubmittedIndex == null) {
                        $outputPerHourAsSubmittedIndex = $index;
                    }

                    // GET INDEX FOR LABOR
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $laborWordToSearchStart), $cleanedLine)) {
                        $laborRowStart = $index;
                    }
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $laborWordToSearchEnd), $cleanedLine)) {
                        $laborRowEnd = $index;
                    }

                    // GET INDEX FOR EQUIPMENT
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $equipmentWordToSearchStart), $cleanedLine)) {
                        $equipmentRowStart = $index;
                    }
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $equipmentWordToSearch2Start), $cleanedLine)) {
                        $equipmentRowStart = $index;
                    }
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $equipmentWordToSearchEnd), $cleanedLine)) {
                        $equipmentRowEnd = $index;
                    }

                    // GET INDEX FOR MATERIAL
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $materialWordToSearchStart), $cleanedLine)) {
                        $materialRowStart = $index;
                    }
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $materialWordToSearch2Start), $cleanedLine)) {
                        $materialRowStart = $index;
                    }
                    elseif (in_array(preg_replace('/[^a-zA-Z0-9]/', '', $materialWordToSearchEnd), $cleanedLine)) {
                        $materialRowEnd = $index;
                    }
                    
                    if(
                        $itemNumberDescriptionIndex != null &&
                        $unitOfMeasurementIndex != null &&
                        $outputPerHourAsSubmittedIndex != null &&
                        $laborRowStart != null &&
                        $laborRowEnd != null &&
                        $equipmentRowStart != null &&
                        $equipmentRowEnd != null &&
                        $materialRowStart != null &&
                        $materialRowEnd != null 
                    ){
                        $isDone = true;
                        break;
                    } 

                    $index++;
                }
                
                if(!$isDone)
                {
                    $messages = [];
                    if($itemNumberDescriptionIndex == null) $messages[] = '"Item No. / Description" keyword not found.';
                    if($unitOfMeasurementIndex == null) $messages[] = '"Unit of Measurement" keyword not found.';
                    if($outputPerHourAsSubmittedIndex == null) $messages[] = '"Output per Hour - As Submitted" keyword not found.';
                    if($laborRowStart == null) $messages[] = '"Designation" keyword not found.';
                    if($laborRowEnd == null) $messages[] = '"Sub - Total for A.1 - As Submitted" keyword not found.';
                    if($equipmentRowStart == null) $messages[] = '"Name and Capacity" or "Name & Capacity" keyword not found.';
                    if($equipmentRowEnd == null) $messages[] = '"Sub - Total for B.1 - As Submitted" keyword not found.';
                    if($materialRowStart == null) $messages[] = '"Name and Specification" or "Name & Specification" keyword not found.';
                    if($materialRowEnd == null) $messages[] = '"Sub - Total for F.1 - As Submitted" keyword not found.';
                    
                    return [
                        'code' => 422,
                        'status' => "Error",
                        'errors' => $messages
                    ];
                }

                $itemNumberData = $this->getDupaDetailLineValue($collection, $itemNumberDescriptionIndex);
                $dupaUnitData = $this->getDupaDetailLineValue($collection, $unitOfMeasurementIndex);
                $outputPerHourData = $this->getDupaDetailLineValue($collection, $outputPerHourAsSubmittedIndex);
                
                if(count($itemNumberData) == 0 || count($dupaUnitData) == 0 || count($outputPerHourData) == 0)
                {
                    return [
                        'code' => 422,
                        'status' => "Error",
                        'errors' => [
                            'The value of Item Number must be on column D/E/F',
                            'The value of Description must be in a column on the right side of the Item Number',
                            'Unit of Measurement and Output per hour values must be the same column as Item Number'
                        ]
                    ];

                }

                $item_number = $itemNumberData['value'];
                $dupa_name = $collection[$itemNumberDescriptionIndex][(int)$itemNumberData['index'] + 1];
                $dupa_unit = $dupaUnitData['value'];
                $output_per_hour = $outputPerHourData['value'];
                
                if(Dupa::where('item_number', $item_number)->exists())
                    return [
                        'code' => 409,
                        'status' => "Error",
                        'errors' => ["Dupa Item number already exist"]
                    ];
                
                $measurementID = UnitOfMeasurement::where('abbreviation', $dupa_unit)->first()?->id;

                if(!$measurementID)
                    $measurementID = UnitOfMeasurement::insertGetId([
                        'name' => $dupa_unit,
                        'abbreviation' => $dupa_unit
                    ]);

                $newlyInsertedDupaID = Dupa::insertGetId([
                    'subcategory_id' => $request['subcategory_id'],
                    'item_number' => $item_number,
                    'description' => $dupa_name,
                    'unit_id' => $measurementID,
                    'output_per_hour' => $output_per_hour,
                    'category_dupa_id'=> $request['category_dupa_id'],
                    'created_at' => Carbon::now(),
                ]);

                $dupaContentID = DupaContent::insertGetId([
                    'dupa_id' => $newlyInsertedDupaID,
                    'created_at' => now(),
                ]);

                // ======================================= SAVE LABOR
                
                    $laborRow = $laborRowStart + 2;

                    for ($laborRows = $laborRow; $laborRows < $laborRowEnd; $laborRows++) {

                        $laborName = $collection[$laborRows][1];

                        if(!empty($collection[$laborRows][2]))
                            $laborName = $collection[$laborRows][2];
                        elseif(!empty($collection[$laborRows][3]))
                            $laborName = $collection[$laborRows][3];


                        // Check if Labor item doesn't exist in database
                        $doesntExists = Labor::where('designation', $laborName)->doesntExist();

                        if ($doesntExists) {
                            $saveLabor = [
                                'designation' => $laborName,
                                'hourly_rate' => $collection[$laborRows][8],
                                'created_at' => now(),
                            ];

                            $labor_id = Labor::insertGetId($saveLabor);

                            $dupaLaborToInsert = [
                                'dupa_content_id' => $dupaContentID,
                                'labor_id' => $labor_id,
                                'no_of_person' => $collection[$laborRows][6],
                                'no_of_hour' => $collection[$laborRows][7],
                                'created_at' => now(),
                            ];

                            DupaLabor::insert($dupaLaborToInsert);

                        }else {

                            $labor = Labor::where('designation', $laborName)->get();
                            $laborID = collect($labor)->pluck('id')->first();

                            $dupaLaborToInsert = [
                                'dupa_content_id' => $dupaContentID,
                                'labor_id' => $laborID,
                                'no_of_person' => $collection[$laborRows][6],
                                'no_of_hour' => $collection[$laborRows][7],
                                'created_at' => now(),
                            ];

                            DupaLabor::insert($dupaLaborToInsert);
                        }

                    }

                // =======================================

                // ======================================= SAVE EQUIPMENT

                    // Locate Minor Tool
                    $minorToolsSearch = 'Minor Tools';
                    $minorToolRow = 0;

                    foreach ($collection as $rowIndex => $row) {
                        foreach ($row as $column) {
                            if (strpos($this->getStringValue($column), $minorToolsSearch) !== false) {
                                $minorToolRowIndex = $rowIndex;
                                $minorToolValue = $column;
                                break;
                            }
                        }
                        $minorToolRow++;
                    }

                    // Locate Equipment Note:
                    $equipNoteSearch = 'Equipment Note:';
                    $equipNoteRow = 0;

                    foreach ($collection as $equipRowIndex => $row) {
                        foreach ($row as $perColumn) {
                            if (strpos($this->getStringValue($perColumn), $equipNoteSearch) !== false) {
                                $equipNoteRowIndex = $equipRowIndex + 1;
                                $equipNoteColumnValue = $perColumn;
                                break;
                            }
                        }
                        $equipNoteRow++;
                    }

                    // Check for the Equipment Note
                    if (!empty($equipNoteRowIndex)) {
                        for ($i=$equipNoteRowIndex; $i < $equipmentRowEnd ; $i++) {

                            $equipmentNote = $collection[$i][1];


                            $dupaEquipmentNote = [
                                'dupa_id' => $newlyInsertedDupaID,
                                'equipment_note' => $equipmentNote,
                            ];
                            DupaEquipmentNotes::insert($dupaEquipmentNote);
                        }
                    }

                    // Save
                    $equipmentRow = $equipmentRowStart + 2;

                    for ($equipmentRows = $equipmentRow; $equipmentRows < $equipmentRowEnd; $equipmentRows++) {

                        $equipmentName = $collection[$equipmentRows][1];

                        if(!empty($collection[$equipmentRows][2]))
                            $equipmentName = $collection[$equipmentRows][2];
                        elseif(!empty($collection[$equipmentRows][3]))
                            $equipmentName = $collection[$equipmentRows][3];

                        // Check if Equipment item doesn't exist in database
                        $equipDoesntExists = Equipment::where('name', $equipmentName)->doesntExist();


                        if ($equipDoesntExists) {

                            if (empty($minorToolValue)) {

                            }else{

                                    if ($minorToolValue == $equipmentName){
                                        $minortoolPercentage = Str::between($minorToolValue, '(', '%');

                                    // SAVE MINOR TOOL PERCENTAGE
                                    DupaContent::where('id', $newlyInsertedDupaID)->update(['minor_tool_percentage' => $minortoolPercentage]);
                                    }
                            }

                            if (empty($equipmentName)) {


                            } elseif (!empty($minorToolValue) &&  $minorToolValue == $equipmentName) {
                                continue;
                            }
                            else {


                                $saveEquipment = [
                                    'name' => $equipmentName,
                                    'hourly_rate' => $collection[$equipmentRows][8],
                                    'created_at' => now(),
                                ];


                                // SAVE NEW EQUIPMENT
                                $equipment_id = Equipment::insertGetId($saveEquipment);

                                $dupaEquipmentToInsert = [
                                    'dupa_content_id' => $dupaContentID,
                                    'equipment_id' => $equipment_id,
                                    'no_of_unit' => $collection[$equipmentRows][6],
                                    'no_of_hour' => $collection[$equipmentRows][7],
                                    'created_at' => now(),
                                ];

                                DupaEquipment::insert($dupaEquipmentToInsert);
                            }

                        }else {

                            $equipment = Equipment::where('name', $equipmentName)->get();
                            $equipmentID = collect($equipment)->pluck('id')->first();

                            $dupaEquipmentToInsert = [
                                'dupa_content_id' => $dupaContentID,
                                'equipment_id' => $equipmentID,
                                'no_of_unit' => $collection[$equipmentRow][6],
                                'no_of_hour' => $collection[$equipmentRow][7],
                                'created_at' => now(),
                            ];

                            DupaEquipment::insert($dupaEquipmentToInsert);

                        }

                    }

                // =======================================

                // ======================================= DUPA MATERIAL

                    // Consumables
                    $consumableSearch = 'Consumables';
                    $consumableRow = 0;

                    foreach ($collection as $rowIndex => $row) {
                        foreach ($row as $column) {
                            if (strpos($this->getStringValue($column), $consumableSearch) !== false) {
                                $consumableRowIndex = $rowIndex;
                                $consumableValue = $column;
                                break;
                            }
                        }
                        $consumableRow++;
                    }

                    // material note
                    $materialNoteSearch = 'Material Note:';
                    $materialNoteRow = 0;

                    foreach ($collection as $materialRowIndex => $row) {
                        foreach ($row as $perColumn) {
                            if (strpos($this->getStringValue($perColumn), $materialNoteSearch) !== false) {
                                $materialNoteRowIndex = $materialRowIndex + 1;
                                $materialNoteColumnValue = $perColumn;
                            }
                        }
                        $materialNoteRow++;
                    }

                    // Check for the Material Note
                    if (!empty($materialNoteRowIndex))
                        for ($i=$materialNoteRowIndex; $i < $materialRowEnd ; $i++) {

                            $materialNote = $collection[$i][1];

                            $dupaMaterialNote = [
                                'dupa_id' => $newlyInsertedDupaID,
                                'material_note' => $materialNote,

                            ];
                            DupaMaterialNotes::insert($dupaMaterialNote);
                        }

                    // save
                    $materialRow = $materialRowStart + 2;

                    for ($materialRows = $materialRow; $materialRows < $materialRowEnd; $materialRows++) {

                        $materialName = $collection[$materialRows][1];

                        if(!empty($collection[$materialRows][2]))
                            $materialName = $collection[$materialRows][2];
                        elseif(!empty($collection[$materialRows][3]))
                            $materialName = $collection[$materialRows][3];


                        // Check if Material item doesn't exist in database
                        $doesntExists = Material::where('name', $materialName)->doesntExist();

                        if ($doesntExists) {

                            if (empty($consumableValue)) {

                            }else{
                                    if ($consumableValue == $materialName){
                                        $consumablePercentage = Str::between($consumableValue, '(', '%');

                                    // SAVE MINOR TOOL PERCENTAGE
                                    DupaContent::where('id', $newlyInsertedDupaID)->update(['consumable_percentage' => $consumablePercentage]);
                                    }
                            }

                            if (empty($materialName)) {


                            } elseif (!empty($consumableValue) &&  $consumableValue == $materialName) {
                                continue;
                            }
                            else {

                                $saveMaterial = [
                                    'name' => $materialName,
                                    'unit' => $collection[$materialRows][6],
                                    'unit_cost' => $collection[$materialRows][8],
                                    'created_at' => now(),
                                ];

                                $material_id = Material::insertGetId($saveMaterial);

                                $dupaMaterialToInsert = [
                                    'dupa_content_id' => $dupaContentID,
                                    'material_id' => $material_id,
                                    'quantity' => $collection[$materialRows][7],
                                    'created_at' => now(),
                                ];

                                DupaMaterial::insert($dupaMaterialToInsert);
                            }

                        }else {

                            $material = Material::where('name', $materialName)->get();
                            $materialID = collect($material)->pluck('id')->first();

                            $dupaMaterialToInsert = [
                                'dupa_content_id' => $dupaContentID,
                                'material_id' => $materialID,
                                'quantity' => $collection[$materialRows][7],
                                'created_at' => now(),
                            ];

                            DupaMaterial::insert($dupaMaterialToInsert);
                        }

                    }

                // ======================================= 
                
                return [
                    'code' => 201,
                    'status' => 'Success',
                    'message' => "Import Successful"
                ];
            });

            return response($transaction, $transaction['code']);


        } catch (\Throwable $th) {

            info($th->getMessage());

            return response([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }

    }

    public function takeOffImport(Request $request) {

    try {

        $file = $request->file('filepond');
        $folder = uniqid(). '-' .now()->timestamp;

        $filepath = $file->store('temp');

        $row = SimpleExcelReader::create(storage_path('app/'.$filepath))->noHeaderRow();

        $rows = $row->getRows();
        $collection = collect($rows->toArray());


        // Locate start and end of table

        $table_start = "LOCATION";
        $table_end = "TOTAL";

        $row = 0;
        $row_input = [];
        $merge = [];

        foreach ($collection as $line) {

            $lines[] = $line;

            // info(in_array($table_start, $line));
            if(in_array($table_start, $line)){
                $merged[]= $row;
            }
            $row++;

        }


        // return 'yes';

        foreach ($merged as $table_row) {
            $input = [];
            $field_ids = [];

            // SAVE TABLE ITEM CODE AND DESCRIPTION
            $table_item_code = $collection[$table_row - 1][0];
            $table_description = $collection[$table_row - 1][1];

            // return $table_item_code;

            $dupa = DUPA::where('item_number', $table_item_code)
            ->join('sow_sub_categories', 'sow_sub_categories.id', 'dupas.subcategory_id')
            ->join('table_dupa_components', 'dupas.id', 'table_dupa_components.dupa_id')
            ->join('table_dupa_component_formulas', 'table_dupa_components.id', 'table_dupa_component_formulas.table_dupa_component_id')
            ->select('dupas.id as dupa_id', 'sow_sub_categories.sow_category_id', 'table_dupa_component_formulas.id as table_dupa_component_formula_id')
            ->first();

            $take_off_tbl = [
                'take_off_id' => $request['take_off_id'],
                'sow_category_id' => $dupa->sow_category_id,
                'table_dupa_component_formula_id' => $dupa->table_dupa_component_formula_id,
            ];

            $table_id = TakeOffTable::insertGetId($take_off_tbl);

            $count = count($collection[$table_row]);
            //  SAVE TABLE FIELDS
            for ($i=1; $i < $count; $i++) {
                if(!empty($collection[$table_row][$i])){

                    $measure = UnitOfMeasurement::where('name', $collection[$table_row][$i])->first();

                    $field = [
                        'take_off_table_id' => $table_id,
                        'measurement_id' => $measure->id,
                    ];

                    $field_ids[] = TakeOffTableFields::insertGetId($field);
                }
            }

            // return $field_ids;

            // FIND THE END OF THE TABLE (TOTAL)
            for ($tbl_end = $table_row; $tbl_end < 1000 ; $tbl_end++) {

                if($collection[$tbl_end][0] == "TOTAL"){
                    break;
                }
            }
            // $maxRowNo = [];

            $maxRowNo = TakeOffTableFieldsInput::max('row_no');

            $nextRowNo = $maxRowNo + 1;


            for ($i=$table_row + 1; $i < $tbl_end; $i++) {

                // SAVE ROW INPUTS

                $row_input = [];

                $wordsearch = 'Contingency';

                if(strpos($this->getStringValue($collection[$i][0]), $wordsearch)){


                    $cont = Str::between($collection[$i][0], ' ', '%');


                    TakeOffTable::where('id', $table_id)->update(['contingency'=> $cont]);
                }

                $keys = 1;
                foreach ($field_ids as $field_id) {


                    // return $field_id;
                    if(!empty($collection[$i][$keys])){
                        $input = [
                            'take_off_table_id' => $table_id,
                            'take_off_table_field_id' => $field_id,
                            'row_no' => $nextRowNo,
                            'value' => $collection[$i][$keys],
                        ];

                        info($input);

                        TakeOffTableFieldsInput::insert($input);

                    }

                    $keys++;
                }

                if(strpos($this->getStringValue($collection[$i][0]), $wordsearch) == false){

                    $mark = [
                        'take_off_table_id' => $table_id,
                        'row_no' => $nextRowNo++,
                        'mark_description' => $collection[$i][0],
                    ];

                    Mark::insert($mark);
                }
            }
        }

        return response()->json([
            'status' => "Success",
            'message' => "Import success"
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => "Error",
            'message' => $th->getMessage()
        ]);
    }


    }
}
