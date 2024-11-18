<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Dupa;
use App\Models\TakeOffTableFields;
use App\Models\TakeOffTable;
use App\Models\Formula;

class UnitOfMeasurement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function dupa()
    {
        return $this->hasOne(Dupa::class, 'unit_id', 'id');
    }

    public function dupaPerProject()
    {
        return $this->hasOne(DupaPerProject::class, 'unit_id', 'id');
    }

    public function takeOffTable()
    {
        return $this->hasMany(TakeOffTable::class, 'table_row_result_field_id');
    }

    public function tableField()
    {
        return $this->hasMany(TakeOffTableFields::class, 'measurement_id', 'id');
    }

    public function formula()
    {
        return $this->hasMany(Formula::class, 'unit_of_measurement_id', 'id');
    }

    // public function dupaMeasure(){
    //     return $this->hasMany(Dupa::class, 'unit_id');
    // }
}
