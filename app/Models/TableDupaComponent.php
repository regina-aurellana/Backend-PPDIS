<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Dupa;
use App\Models\TableDupaComponentFormula;
use App\Models\TakeOffTable;

class TableDupaComponent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dupa(){
        return $this->hasMany(Dupa::class, 'id');
    }

    public function tableDupaComponentFormula(){
        return $this->belongsTo(TableDupaComponentFormula::class, 'id', 'table_dupa_component_id');
    }

    public function takeOffTable(){
        return $this->belongsTo(TakeOffTable::class);
    }


}
