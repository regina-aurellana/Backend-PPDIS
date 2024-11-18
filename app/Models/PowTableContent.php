<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ABCContent;

class PowTableContent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function abcContent()
    {
        return $this->belongsTo(ABCContent::class);
    }

    public function programOfWork()
    {
        return $this->belongsTo(ProgramOfWork::class);
    }

    // public function dupa() //per project no connection just remove
    // {
    //     return $this->hasMany(Dupa::class, 'id');
    // }

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


    public function sowSubcategory()
    {
        return $this->belongsTo(SowSubCategory::class);
    }

    public function powTable()
    {
        return $this->belongsTo(PowTable::class, 'id');
    }

    public function dupaItemsPerProject()
    {
        return $this->hasMany(PowTableContentDupa::class, 'pow_table_content_id', 'id');
    }
}
