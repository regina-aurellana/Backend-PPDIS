<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryDupa;

use App\Http\Requests\CategoryDupa\CategoryDupaRequest;

class CategoryDupaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cat_dupa = CategoryDupa::get();

        return response()->json($cat_dupa);
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
    public function store(CategoryDupaRequest $request)
    {
        try {
            CategoryDupa::updateOrCreate(
                ['id' => $request['id']],
                ['name' => $request['name']]
            );

            return response()->json([
                'status' => 'Success',
                'message' => 'Successfully Added'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryDupa $category_dupa)
    {
        $cat_dupa = CategoryDupa::find($category_dupa)->first();

        return response()->json($cat_dupa);
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
    public function destroy(CategoryDupa $category_dupa)
    {
        try {
            $category_dupa->delete();

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
}
