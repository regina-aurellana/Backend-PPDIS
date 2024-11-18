<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\DupaContent;
use App\Models\UnitOfMeasurement;
use App\Models\CategoryDupa;
use App\Models\ProjectNature;
use App\Models\TakeOffTable;
use App\Models\Formula;
use App\Models\SowSubCategory;
use App\Models\TableDupaComponent;
use App\Models\PowTableContentDupa;


class Dupa extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'subcategory_id',
        'item_number',
        'description',
        'unit_id',
        'category_dupa_id',
        'output_per_hour',
    ];

    public function categoryDupa()
    {
        return $this->belongsTo(CategoryDupa::class, 'category_dupa_id', 'id');
    }

    public function dupaContent()
    {
        return $this->hasOne(DupaContent::class);
    }

    public function measures()
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'unit_id');
    }

    public function takeOffTable()
    {
        return $this->belongsTo(TakeOffTable::class, 'dupa_id');
    }

    public function sowSubcategory()
    {
        return $this->belongsTo(SowSubCategory::class, 'id');
    }

    // public function powContent()
    // {
    //     return $this->belongsTo(PowTableContent::class, 'dupa_id');
    // }

    public function tableDupaComponent()
    {
        return $this->belongsTo(TableDupaComponent::class, 'dupa_id', 'id');
    }

    public function dupaPerProject()
    {
        return $this->hasMany(DupaPerProject::class, 'dupa_id', 'id');
    }
}
