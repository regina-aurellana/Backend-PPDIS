<?php

namespace App\Http\Controllers;

use App\Http\Requests\PowTableContent\UpdateQuantityRequest;
use App\Models\Dupa;
use App\Models\DupaPerProject;
use App\Models\PowTable;
use App\Models\PowTableContent;
use App\Models\PowTableContentDupa;
use Illuminate\Http\Request;

class POWTableContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $content = PowTableContentDupa::with([
            'dupa' => function ($q) {
                $q->select(
                    'id',
                    'item_number',
                    'description',
                    'direct_unit_cost',
                    'unit_id'
                );

                $q->with([
                    'measures' => function ($q) {
                        $q->select('id', 'name', 'abbreviation');
                    }
                ]);
            },
            'content' => function ($q) {
                $q->select(
                    'id',
                    'pow_table_id',
                    'sow_category_id',
                    'sow_subcategory_id'
                );

                $q->with(['sowSubcategory' => function ($q) {
                    $q->select('id', 'item_code', 'name');
                }]);
            }

        ])
            ->get();


        return response()->json($content);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PowTableContentDupa $pow_table_content)
    {
        try {

            $pow_content = PowTableContentDupa::where('id', $pow_table_content->id)
                ->with([
                    'dupa' => function ($q) {
                        $q->select(
                            'id',
                            'item_number',
                            'description',
                            'direct_unit_cost',
                            'unit_id'
                        );

                        $q->with([
                            'measures' => function ($q) {
                                $q->select('id', 'name', 'abbreviation');
                            }
                        ]);
                    },
                    'content' => function ($q) {
                        $q->select(
                            'id',
                            'pow_table_id',
                            'sow_category_id',
                            'sow_subcategory_id'
                        );

                        $q->with(['sowSubcategory' => function ($q) {
                            $q->select('id', 'item_code', 'name');
                        }]);
                    }

                ])
                ->first();



            // ->join('pow_table_contents', 'pow_table_contents.id', 'pow_table_content_dupas.pow_table_content_id')
            // ->select('pow_table_contents.sow_category_id', 'pow_table_content_dupas.id as content_dupa_id', 'pow_table_content_dupas.pow_table_content_id')

            return $pow_content;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
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

    public function updateQuantity(UpdateQuantityRequest $request, PowTableContentDupa $pow_table_content_dupa)
    {
        try {
            $direct_unit_cost = DupaPerProject::where('id', $pow_table_content_dupa->dupa_per_project_id)->first();

            $pow_table_content_dupa->update([
                'quantity' => $request->quantity,
                'total_estimated_direct_cost' => floatval($direct_unit_cost->direct_unit_cost ?? 0) * floatval($request->quantity),
            ]);

            return response()->json([
                'status' => 'Success',
                'Message' => 'Pow Table Content - Quantity Successfully Updated'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Success',
                'message' => $th->getMessage()
            ]);
        }
    }

    // Remove or Delete DUPA in POW
    public function destroy(PowTableContent $pow_table_content)
    {
        try {

            foreach ($pow_table_content->dupaItems as $item) {

                $item->delete();
            }

            $pow_table_content->delete();


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


    public function destroyContentDupa(PowTableContentDupa $content)
    {
        try {

            $content->delete();


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
}
