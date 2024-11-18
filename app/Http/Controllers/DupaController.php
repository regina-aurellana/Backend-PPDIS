<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Dupa\AddDupaRequest;
use App\Http\Services\Dupa\DupaService;
use App\Http\Services\Files\ExportService;
use App\Models\Dupa;
use App\Models\DupaContent;
use App\Models\ProjectNature;
use App\Models\CategoryDupa;
use App\Models\DupaPerProject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DupaController extends Controller
{

   public function index(){

        $this->computeDirectUnitCost();

        $dupa = Dupa::join('category_dupas', 'category_dupas.id', 'dupas.category_dupa_id')
            ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupas.unit_id')
            ->join('sow_sub_categories', 'sow_sub_categories.id', 'dupas.subcategory_id')
            ->select('dupas.id', 'dupas.item_number', 'sow_sub_categories.name as scope_of_work_subcategory', 'dupas.description', 'unit_of_measurements.abbreviation', 'dupas.direct_unit_cost', 'category_dupas.name as dupa_category')
            ->orderBy('dupas.id')
            ->paginate(5000);


        return response()->json($dupa);

   }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddDupaRequest $request)
    {
        try {
            $exist = Dupa::where('item_number', $request['item_number'])
            ->when($request->id, function($q) use($request){
                $q->whereNot('id', $request->id);
            })
            ->exists();

            if ($exist){

                return response([
                    'status' => "Warning",
                    'message' => "Dupa Item number already exist"
                ], 409);

            }

           $dupa = Dupa::updateOrCreate(
                ['id' => $request['id']],
                [
                    'item_number' => $request['item_number'],
                    'subcategory_id' => $request['subcategory_id'],
                    'description' => $request['description'],
                    'unit_id' => $request['unit_id'],
                    'output_per_hour' => $request['output_per_hour'],
                    'category_dupa_id' => $request['category_dupa_id'],
                ]
            );
            if ($dupa->wasRecentlyCreated) {

                DupaContent::create(['dupa_id' => $dupa->id]);

                return response()->json([
                    'status' => "Created",
                    'message' => "Dupa Successfully Created"
                ]);
            }else{
                return response()->json([
                    'status' => "Updated",
                    'message' => "Dupa Successfully Updated "
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "Error",
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Dupa $dupa)

    {
        $dupa = Dupa::where('dupas.id', $dupa->id)
        ->join('unit_of_measurements', 'unit_of_measurements.id', 'dupas.unit_id')
        ->join('category_dupas', 'category_dupas.id', 'dupas.category_dupa_id')
        ->join('sow_sub_categories', 'sow_sub_categories.id', 'dupas.subcategory_id')
        ->select('dupas.*', 'unit_of_measurements.abbreviation','category_dupas.name as dupa_category', 'sow_sub_categories.name as sow_subcategory')
        ->first();

        return response()->json($dupa);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dupa $dupa)
    {
        try {

            $dupa->dupaContent()->delete();

            $dupa->delete();

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

    private function computeDirectUnitCost(){

        $service = new DupaService;

        $service->computeDirectUnitCost();
    }

    public function exportDupa()
    {
        $exporter = new ExportService;
        $dupas = Dupa::with([
            'measures' => function($query){
                $query->select('id', 'name');
            },
            'sowSubcategory' => function($query) {
                $query->select('id', 'name');
            },
            'sowSubcategory.parentSubCategory',
            'categoryDupa' => function($query) {
                $query->select('id', 'name');
            }
        ])
        ->select('id', 'item_number', 'subcategory_id', 'unit_id', 'category_dupa_id')
        ->get();
        
        $data = [];
        foreach ($dupas as $dupa) {

            $subcat = $dupa->sowSubcategory ? $dupa->sowSubcategory->name : '';
            $parent_subcat = $dupa->sowSubcategory ? 
                                (count($dupa->sowSubcategory->parentSubCategory) > 0 ? $dupa->sowSubcategory->parentSubCategory[0]->name : '' ) 
                            : '';
            
            $data[] = [
                'Unit Code' => $dupa->item_number,
                'Unit' => $dupa->measures->name,
                'Category' => $dupa->categoryDupa->name,
                'Parent Sub Category' => $parent_subcat,
                'Sub Category' => $subcat,
            ];

        }
        
        $writer = $exporter->export($data);

        return response()->json($writer, 200);
    }
}