<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\EquipmentComponent;
use App\Models\TemporaryFile;
use App\Http\Requests\Equipment\AddEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Http\Requests\Equipment\EquipmentComponentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentComponentRequest;
use App\Http\Services\Dupa\DupaService;
use App\Http\Services\Files\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\LazyCollection;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $equipment = Equipment::with('equipmentComponent')->get();

       return response()->json($equipment);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddEquipmentRequest $equipRequest, EquipmentComponentRequest $componentRequest)
    {
        try {

            $deleted_equipment_exists = Equipment::onlyTrashed()
            ->where(function($query) use($equipRequest){
                $query->where('name', $equipRequest->name)
                ->orWhere('item_code', $equipRequest->item_code);
            })->first();

            if($deleted_equipment_exists)
            {
                $deleted_equipment_exists->restore();
                $deleted_equipment_exists->update([
                    'hourly_rate' => $equipRequest['hourly_rate']
                ]);

                return response()->json([
                    'status' => "Restored",
                    'message' => "We've found the same Equipment from the database and restored it for you."
                ]);
            }

            $id = $equipRequest->equipment_id;

            if($id)
            {
                //UPDATE
                $equipment = Equipment::whereNot('id', $id)
                ->where(function($query) use($equipRequest){
                    $query->where('name', $equipRequest->name)
                    ->orWhere('item_code', $equipRequest->item_code);
                })
                ->first();
                
                if($equipment)
                {
                    if($equipment->name == $equipRequest->name)
                    {
                        return response()->json([
                            'status' => "Error",
                            'message' => 'The name ' . $equipRequest->name . " already exists."
                        ], 422);
                    }
                    if($equipment->item_code == $equipRequest->item_code)
                    {
                        return response()->json([
                            'status' => "Error",
                            'message' => 'The item code ' . $equipRequest->item_code . " already exists."
                        ], 422);
                    }
                }

                Equipment::where('id', $id)->update([
                    'item_code' => $equipRequest->item_code,
                    'name' => $equipRequest->name,
                    'hourly_rate' => $equipRequest->hourly_rate
                ]);

                $service = new DupaService;
                $service->computeDirectUnitCost();

            }
            else
            {
                //CREATE
                $equipment = Equipment::create([
                    'item_code' => $equipRequest->item_code,
                    'name' => $equipRequest->name,
                    'hourly_rate' => $equipRequest->hourly_rate
                ]);

                $id = $equipment->id;

            }

            EquipmentComponent::where('equip_id', $id)->delete();

            $component = [];

            foreach ($componentRequest->component_name as $name) {
                $component[] = [
                    'equip_id' => $id,
                    'component_name' => $name
                ];
            }

            EquipmentComponent::insert($component);

            return response()->json([
                'status' => 'Created',
                'message' => 'Equipment Saved.'
            ]);

        } catch (\Throwable $th) {

            info('Save equipment error: ' . $th->getMessage());

            return response()->json([
                'status' => 'erroe',
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        $equipments = Equipment::where('id', $equipment->id)
        ->with('equipmentComponent')
        ->select('id', 'item_code', 'name', 'hourly_rate')
        ->first();

       return response()->json($equipments);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentRequest $equipRequest, UpdateEquipmentComponentRequest $componentRequest, Equipment $equipment)
    {
        try {

            $equipment->update([
                'item_code' => $equipRequest['item_code'],
                'name' => $equipRequest['name'],
                'hourly_rate' => $equipRequest['hourly_rate'],
                'updated_at' => now()
            ]);


            if($equipment->equipmentComponent){

                if ($componentRequest->component_name){

                    $existing_component = EquipmentComponent::where('equip_id', $equipment->id)->delete();

                    foreach ($componentRequest->component_name as $component_name) {

                            $equip_comp[] = [
                                'equip_id' => $equipment->id,
                                'component_name' => $component_name,
                                'created_at' => now()
                            ];
                    }
                    EquipmentComponent::insert($equip_comp);

                    $service = new DupaService;
                    $service->computeDirectUnitCost();

                    return response()->json([
                        'status' => 'Created',
                        'message' => 'Equipment Successfully Updated'
                    ]);

                } else{
                    $existing_component = EquipmentComponent::where('equip_id', $equipment->id)->delete();
                }
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        try {

            $equipment->delete();

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

    public function uploadEquipment(Request $request) {

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

            return '';
        }
    }

    public function revertEquipment(){

        $folder = request()->getContent();

        TemporaryFile::where('folder', $folder)->delete();
        Storage::deleteDirectory('temp/'.$folder);
        return '';
    }

    public function importEquipment(Request $request) {
        try {

            $file = $request->file('filepond');
            $folder = uniqid(). '-' .now()->timestamp;

            $filepath = $file->store('temp');
            
            $headers = SimpleExcelReader::create(storage_path('app/'.$filepath))->getHeaders();

            if(!in_array('Equipment Code', $headers) || !in_array('Equipment Description', $headers) || !in_array('Hourly Rate', $headers))
                return response()->json([
                    'status' => "Error",
                    'message' => "Excel file incorrectly formatted. Make sure you have headers Column A: Equipment Code, Column B: Equipment Description, Column C: Hourly Rate"
                ], 422);

            $rows = SimpleExcelReader::create(storage_path('app/'.$filepath))->getRows();
            $collection = collect($rows->toArray());

            $item_code = $collection->pluck('Equipment Code');
            $name = $collection->pluck('Equipment Description');
            $hourly_rate = $collection->pluck('Hourly Rate');

            for ($equip = 0; $equip < count($item_code); $equip++) {

                $item_codes [] = $item_code[$equip];
                $data [] = [
                    'item_code' => $item_code[$equip],
                    'name' => $name[$equip],
                    'hourly_rate' => $hourly_rate[$equip]
                ];
            }

            $existing_item_code = Equipment::select('item_code')->whereIn('item_code', $item_codes)->pluck('item_code');

            $data_to_insert = [];

            foreach ($data as $insert) 
            {
                if (in_array($insert['item_code'], $existing_item_code->toArray())) {
                    Equipment::where('item_code', $insert['item_code'])->update([
                        'item_code' => $insert['item_code'],
                        'name' => $insert['name'],
                        'hourly_rate' => $insert['hourly_rate'],
                    ]);
                }
                else{
                    $data_to_insert[] = [
                        'item_code' => $insert['item_code'],
                        'name' => $insert['name'],
                        'hourly_rate' => $insert['hourly_rate'],
                        'created_at' => now(),
                    ];
                }
            }

            foreach (array_chunk($data_to_insert, 1000) as $data) {
                Equipment::insert($data);
            }

            $service = new DupaService;
            $service->computeDirectUnitCost();

            return response()->json([
                'status' => "Success",
                'message' => "Imported Successfully"
            ]);


        } catch (\Throwable $th) {

           info('Import Equipment error: ' . $th->getMessage());

           return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function exportEquipment()
    {
        $exporter = new ExportService;
        $data = Equipment::select(
            'item_code AS Equipment Code', 
            'name AS Equipment Description', 
            'hourly_rate AS Hourly Rate'
        )->get()->toArray();
        $writer = $exporter->export($data);

        return response()->json($writer, 200);
    }
}
