<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\UnitOfMeasurement;
use App\Models\Dupa;
use App\Models\TableDupaComponentFormula;

class Formula extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function measurement() {
        return $this->belongsTo(UnitOfMeasurement::class, 'unit_of_measurement_id');
    }

    public function dupa(){
        return $this->hasMany(Dupa::class, 'dupa_id', 'id');
    }

    public function tableDupaComponentFormula() {
        return $this->hasMany(TableDupaComponentFormula::class, 'id');
    }




}
