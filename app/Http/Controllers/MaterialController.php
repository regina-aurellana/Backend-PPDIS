<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\TemporaryFile;
use App\Http\Requests\Material\AddMaterialRequest;
use App\Http\Services\Dupa\DupaService;
use App\Http\Services\Files\ExportService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $material = Material::get();

        return response()->json($material);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddMaterialRequest $request)
    {

        try {

            $deleted_material_exists = Material::onlyTrashed()
            ->where(function($query) use($request){
                $query->where('name', $request->name)
                ->orWhere('item_code', $request->item_code);
            })->first();

            if($deleted_material_exists)
            {
                $deleted_material_exists->restore();
                $deleted_material_exists->update([
                    'unit' => $request->unit,
                    'unit_cost' => $request->unit_cost,
                ]);

                return response()->json([
                    'status' => "Restored",
                    'message' => "We've found the same Material from the database and restored it for you."
                ]);
            }

            $id = $request->material_id;

            if($id)
            {
                //UPDATE
                $material = Material::whereNot('id', $id)
                ->where(function($query) use($request){
                    $query->where('name', $request->name)
                    ->orWhere('item_code', $request->item_code);
                })
                ->first();
                
                if($material)
                {
                    if($material->name == $request->name)
                    {
                        return response()->json([
                            'status' => "Error",
                            'message' => 'The name ' . $request->name . " already exists."
                        ], 422);
                    }
                    if($material->item_code == $request->item_code)
                    {
                        return response()->json([
                            'status' => "Error",
                            'message' => 'The item code ' . $request->item_code . " already exists."
                        ], 422);
                    }
                }

                Material::where('id', $id)->update([
                    'item_code' => $request->item_code,
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'unit_cost' => $request->unit_cost,
                ]);

                $service = new DupaService;
                $service->computeDirectUnitCost();

            }
            else
            {
                //CREATE
                Material::create([
                    'item_code' => $request->item_code,
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'unit_cost' => $request->unit_cost,
                ]);

            }

            return response()->json([
                'status' => 'Created',
                'message' => 'Material Saved.'
            ]);

        } catch (\Throwable $th) {
            
            info('Saving material error: ' . $th->getMessage());

            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        $mat = Material::where('id', $material->id)
        ->select('id', 'item_code', 'name', 'unit', 'unit_cost')
        ->first();

        return response()->json($mat);
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
    public function update(AddMaterialRequest $request, Material $material)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        try {
            $material->delete();

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

    public function uploadMaterial(Request $request){

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
            info('Temporary file upload error: ' . $th->getMessage());
            return '';
        }

    }
    public function revertMaterial(){

        $folder = request()->getContent();

        TemporaryFile::where('folder', $folder)->delete();
        Storage::deleteDirectory('temp/'.$folder);

        return '';

    }

    public function import(Request $request){

        try {
            $file = $request->file('filepond');
            $folder = uniqid(). '-' .now()->timestamp;

            $filePath = $file->store('temp');

            $headers = SimpleExcelReader::create(storage_path('app/'.$filePath))->getHeaders();

            if(!in_array('Material Code', $headers) || !in_array('Material Description', $headers) || !in_array('Unit', $headers) || !in_array('Average', $headers))
                return response()->json([
                    'status' => "Error",
                    'message' => "Excel file incorrectly formatted. Make sure you have headers Column \nA: Material Code, \nColumn B: Material Description, \nColumn C: Unit, \nColumn D: Average"
                ], 422);

            $rows = SimpleExcelReader::create(storage_path('app/'.$filePath))->getRows();
            $collection = collect($rows->toArray());


            $item_code = $collection->pluck('Material Code');
            $name = $collection->pluck('Material Description');
            $unit = $collection->pluck('Unit');
            $unit_cost = $collection->pluck('Average');

            $data = [];
            $item_codes = [];
            $data_to_insert = [];

            //STORE TO TEMP VARIABLE
            for ($mat = 0; $mat < count($item_code); $mat++) {

                $item_codes [] = $item_code[$mat];
                $data[] = [
                    'item_code' => $item_code[$mat],
                    'name' => $name[$mat],
                    'unit' => $unit[$mat],
                    'unit_cost' => $unit_cost[$mat],
                ];
            }

            //GET EXISTING ITEM CODES
            $existing_item_codes = Material::select('item_code')->whereIn('item_code', $item_codes)->pluck('item_code');
            $data_to_insert = [];

            //UPDATE EXISTING THEN PREPARE DATA TO INSERT
            foreach($data as $insert){

                if(in_array($insert['item_code'], $existing_item_codes->toArray())){
                    Material::where('item_code', $insert['item_code'])->update([
                        'name' => $insert['name'],
                        'unit' => $insert['unit'],
                        'unit_cost' => $insert['unit_cost'],
                    ]);
                }
                else {
                    $data_to_insert[] = [
                        'item_code' => $insert['item_code'],
                        'name' => $insert['name'],
                        'unit' => $insert['unit'],
                        'unit_cost' => $insert['unit_cost'],
                        'created_at' => now()
                    ];
                }

            }

            foreach(array_chunk($data_to_insert, 1000) as $data) {
                Material::insert($data);
            }

            $service = new DupaService;
            $service->computeDirectUnitCost();

            return response()->json([
                'status' => "Success",
                'message' => "Imported Successfully"
            ]);
        } catch (\Throwable $th) {
            info('Import Material error: ' . $th->getMessage());

           return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ], 500);
        }

    }

    public function export()
    {
        $exporter = new ExportService;
        $data = Material::select(
            'item_code AS Material Code', 
            'name AS Material Description',
            'unit AS Unit', 
            'unit_cost AS Average'
        )->get()->toArray();
        $writer = $exporter->export($data);

        return response()->json($writer, 200);
    }


}
