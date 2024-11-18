<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SowCategory;
use App\Models\Dupa;
use App\Models\SowReference;

class SowSubCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'item_code',
        'name',
        'sow_category_id',
    ];

    public function sowCategory()
    {
        return $this->belongsTo(SowCategory::class);
    }

    public function dupa()
    {
        return $this->hasMany(Dupa::class, 'subcategory_id', 'id');
    }

    public function dupaPerProject()
    {
        return $this->hasMany(DupaPerProject::class, 'subcategory_id', 'id');
    }

    public function children()
    {
        return $this->hasManyThrough(
            SowSubCategory::class,
            SowReference::class,
            'parent_sow_sub_category_id',
            'id',
            'id',
            'sow_sub_category_id',
        );
    }

    public function parentSubCategory()
    {
        return $this->hasManyThrough(
            SowSubCategory::class,
            SowReference::class,
            'sow_sub_category_id',
            'id',
            'id',
            'parent_sow_sub_category_id',
        );
    }

    public function reference()
    {
        return $this->hasMany(SowReference::class, 'parent_sow_sub_category_id', 'id');
    }

    public function getAllChildrenSubCategory($subcat)
    {
        $categories = $subcat->children;

        foreach ($categories as $category) {
            $category->children = $this->getAllChildrenSubCategory($category);
        }
        return $categories;
    }

    public function getAllParentSubCategory($subcat)
    {
        $categories = $subcat->parentSubCategory;

        foreach ($categories as $category) {
            $category->getAllParentSubCategory($category);
        }

        return $categories;
    }

    public function findBaseParentSubCategory($subcat)
    {
        $parentSubCategory = $subcat->parentSubCategory;

        // If there are no more parent subcategories, return the current subcategory
        if ($parentSubCategory->isEmpty()) {
            return $subcat;
        }

        // Recursively call the function on the first parent subcategory
        return $this->findBaseParentSubCategory($parentSubCategory->first());

        return $parentSubCategory;
    }


    public function findLastChildSubCategory($subcat)
    {
        $children = $subcat->children;

        // If there are no more parent subcategories, return the current subcategory
        if (!$children->isEmpty()) {
            return $this->findLastChildSubCategory($children->first());
        }
        return $subcat;

        return $children;
    }


    public function findAllLastChildSubCategory($subcat)
    {
        $childrens = $subcat->children;

        // If there are no more parent subcategories, return the current subcategory
        if ($childrens->isEmpty()) {
            return [$subcat];
        }

        $childSubCategories = [];

        foreach ($childrens as $child) {
            $childSubOfChild = $this->findAllLastChildSubCategory($child);

            $childSubCategories = array_merge($childSubCategories, $childSubOfChild);
        }

        return $childSubCategories;
    }
}
