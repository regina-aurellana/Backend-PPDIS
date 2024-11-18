<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\TableDupaComponent;
use App\Models\Formula;

class TableDupaComponentFormula extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function tableDupaComponent(){
        return $this->hasMany(TableDupaComponent::class, 'id', 'table_dupa_component_id');
    }


    public function formula(){
        return $this->belongsTo(Formula::class, 'formula_id');
    }

}
