<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class DupaPerProject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dupa()
    {
        return $this->belongsTo(Dupa::class);
    }

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class, 'b3_project_id');
    }

    public function dupaContentPerProject()
    {
        return $this->hasOne(DupaContentPerProject::class);
    }

    public function measures()
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'unit_id');
    }

    public function sowSubcategory()
    {
        return $this->belongsTo(SowSubCategory::class, 'id');
    }

    public function sowCategory()
    {
        return $this->belongsTo(SowCategory::class, 'id');
    }

    public function dupaPerProjectGroup()
    {
        return $this->belongsTo(DupaPerProjectGroup::class, 'dupa_per_project_group_id', 'id');
    }


    public function workScheduleItem()
    {
        return $this->hasOne(WorkScheduleItem::class, 'dupa_per_project_id', 'id');
   
    }
}
