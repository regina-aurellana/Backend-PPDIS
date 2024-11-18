<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SubCatReference;

class SubCatReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subcat_ref = SubCatReference::with('parent', 'SubCategory')
        ->get();

        return response()->json($subcat_ref);
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
    public function show(SubCatReference $reference)
    {
        $subcat_ref = SubCatReference::where('sub_cat_references.id', $reference->id)
        ->join('sow_sub_categories', 'sow_sub_categories.id', 'sub_cat_references.parent_id')
        ->select('sow_sub_categories.item_code')
        ->first();

        return response()->json($subcat_ref);
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
    public function destroy(string $id)
    {
        //
    }
}
