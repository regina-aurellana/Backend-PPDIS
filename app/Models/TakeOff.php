<?php

namespace App\Models;

use App\Models\Dupa;
use App\Models\B3Projects;
use App\Models\TakeOffTable;

use App\Models\DupaPerProjectGroup;
use Illuminate\Database\Eloquent\Model;
// use App\Models\TakeOffTableFormula;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TakeOff extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function b3Projects()
    {
        return $this->belongsTo(B3Projects::class, 'id');
    }

    public function group()
    {
        return $this->belongsTo(DupaPerProjectGroup::class, 'id');
    }

    public function takeOffTable()
    {
        return $this->hasMany(TakeOffTable::class, 'take_off_id', 'id');
    }

    // public function takeOffTableFormula(){
    //     return $this->hasMany(TakeOffTableFormula::class, 'take_off_table_id');
    // }

    public function dupa()
    {
        return $this->hasMany(Dupa::class);
    }

    public function getTakeOffTableFieldsInputDatasAttribute()
    {
        return $this->takeOffTable->map(function ($table) {
            return $table->takeOffTableFieldsInputDatas->groupBy('row_no');
        });
    }
}
