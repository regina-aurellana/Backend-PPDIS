<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DupaEquipmentPerProject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function dupaContentPerProject()
    {
        return $this->belongsTo(DupaContentPerProject::class);
    }
}
