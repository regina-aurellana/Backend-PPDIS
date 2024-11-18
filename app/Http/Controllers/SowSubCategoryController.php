<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SowSubCategory\SowSubCategoryRequest;
use App\Models\SowSubCategory;
use App\Models\SowCategory;
use App\Models\SowReference;
use App\Models\PowTableContent;

class SowSubCategoryController extends Controller
{

    public function index()
    {
        $subcat = SowSubCategory::with(['sowCategory:id,item_code,name', 'parentSubCategory:item_code,name'])
        ->orderBy('name', 'asc')
        ->get();

        return response()->json($subcat);

    }


    public function create()
    {

    }

    public function store(SowSubCategoryRequest $request)
    {
        try {

            $parent_sub_cat_id = $request->input('parent_sub_cat_id');

            $subcat = SowSubCategory::updateOrCreate(
                ['id' => $request['id']],
                [
                    'item_code' => $request['item_code'],
                    'name' => $request['name'],
                    'sow_category_id' => $request['sow_category_id'],
                ]
        );

            SowReference::updateOrCreate(
                ['sow_sub_category_id' => $subcat->id],
                ['parent_sow_sub_category_id' => $parent_sub_cat_id]

            );
/*
            fetch subcategory
            $main_category = SowCategory::find(1)->with('sowSubCategory');

            fetch all subsub from main_sub_category
            $main_sub_category = SowSubCategory::where('id', 2)->first();
            $data = $main_sub_category->getAllChildrenSubCategory($main_sub_category);


            fetch all subsub from main_sub_category
            $main_sub_category = SowSubCategory::find(1);
            $main_sub_category->getAllChildrenSubCategory();

            fetch subsub
            $subcat = SowSubCategory::where('id', $id)->with('children')->get();

            */


             if ($subcat->wasRecentlyCreated) {
                 return response()->json([
                     'status' => "Created",
                     'message' => "SubCat Successfully Created"
                 ]);
             }else{
                 return response()->json([
                     'status' => "Updated",
                     'message' => "SubCat Successfully Updated"
                 ]);
             }

         } catch (\Throwable $th) {
             return response()->json([
                 'status' => "Error",
                 'message' => $th->getMessage()
             ]);
         }
    }


    public function show(SowSubCategory $subcat)
    {
        // DISPLAY SOW CATEGORY AND PARENT SUBCATEGORY
        $subcat = SowSubCategory::where('id', $subcat->id)
        ->with(['sowCategory:id,item_code,name', 'parentSubCategory:item_code,name'])
        ->first();


        return response()->json($subcat);

    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(SowSubCategory $subcat)
    {
        try {

            $subcat->reference()->delete();
            $subcat->delete();

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


    public function sowcatList(SowCategory $subcat){

        $subcat = SowSubCategory::where('sow_category_id', $subcat->id)
        ->get();

        return $subcat;
    }

    // public function sowcatList(SowSubCategory $subcat){

    //     // DISPLAY ALL CHILDREN SUBCATEGORY OF A SOWCAT ID

    //         $main_category = SowCategory::where('id', $subcat->id)->first();

    //         $main_data = $main_category->sowSubCategory()->first();

    //             if($main_data == null){
    //                 return "No descendants to show";
    //             }else{

    //                 $main_data->getAllChildrenSubCategory($main_data);

    //                 return $main_data;
    //             }
    //     }

        // GET ALL THE DECSENDANTS OF SUBCATEGORY
            public function sowcatDescendants(SowSubCategory $subcat){

                    $main_category = SowSubCategory::where('id', $subcat->id)->first();

                    $main_data = $main_category->getAllChildrenSubCategory($main_category);

                    return $main_data;
                }


            public function getDupaOfSubcategory(SowSubCategory $subcat){

                $main_category = SowSubCategory::where('id', $subcat->id)->first();

                    if(!$main_category->dupa->isEmpty()){

                        $dupaList = $main_category->dupa;

                        return $dupaList;
                }
                else{
                    return "No Dupa to show";
                }
            }

            public function getDupaOfSowcategory(SowCategory $subcat){

                $subcats = SowSubCategory::where('sow_category_id', $subcat->id)->get();
                $mergedDupa = [];

                foreach ($subcats as $subcat) {
                    $dupa = $subcat->dupa;

                    if(!$dupa->isEmpty())
                    $mergedDupa = array_merge($mergedDupa, $dupa->toArray());
                }

                if(!$mergedDupa)
                    return "No Dupa to show";

                 return $mergedDupa;

            }


        // GET ALL THE ANCESTOR OF SUBCATEGORY
                public function sowcatAncestor(SowSubCategory $subcat){

                        $main_category = SowSubCategory::where('id', $subcat->id)->first();

                        $main_data = $main_category->getAllParentSubCategory($main_category);

                        return $main_data;

                    }

        // GET ALL THE BASE PARENT OF SUBCATEGORY
                public function getBaseParent(SowSubCategory $subcat) {

                    // $main_category = SowSubCategory::where('id', $subcat->id)->first();

                    $baseParent = $subcat->findBaseParentSubCategory($subcat);

                    return $baseParent;
                    }

                // GET THE lAST CHILD OF SUBCATEGORY
                public function getLastChild(SowSubCategory $subcat) {

                    $main_category = SowSubCategory::where('id', $subcat->id)->first();

                    $baseParent = $main_category->findLastChildSubCategory($main_category);

                    return $baseParent;
                    }

                // GET ALL SUBCAT BASE PARENT OF DUPA
                public function dupaBaseSubcatParent(PowTableContent $subcat){

                    $pow = PowTableContent::where('id', $subcat->id)
                    ->value('sow_subcategory_id');

                    $main_category = SowSubCategory::where('id', $pow)->first();

                    $baseParent = $main_category->findBaseParentSubCategory($main_category);

                    return $baseParent;


                }

                public function firstLevelSub(SowCategory $subcat){

                   $firstLvl = SowSubCategory::where('sow_category_id', $subcat->id )
                   ->join('sow_categories', 'sow_categories.id', 'sow_sub_categories.sow_category_id')
                   ->join('sow_references', 'sow_sub_categories.id', 'sow_references.sow_sub_category_id')
                   ->select('sow_sub_categories.*', 'sow_references.*')
                   ->whereNull('sow_references.parent_sow_sub_category_id')
                   ->get();

                   return $firstLvl;
                }

                public function secondLevelSub(SowSubCategory $subcat){

                    $secondLvl = SowSubCategory::where('id', $subcat->id )
                    ->with('children')
                    ->get();

                    return $secondLvl;
                }



}
