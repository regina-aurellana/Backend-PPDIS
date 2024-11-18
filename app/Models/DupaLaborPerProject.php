<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DupaLaborPerProject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function labor()
    {
        return $this->belongsTo(Labor::class);
    }

    public function dupaContentPerProject()
    {
        return $this->belongsTo(DupaContentPerProject::class);
    }
}
