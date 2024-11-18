<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Equipment;

class EquipmentComponent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function equipment(){
        return $this->belongsTo(Equipment::class);
    }
}
