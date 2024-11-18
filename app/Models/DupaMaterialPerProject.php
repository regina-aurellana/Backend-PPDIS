<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DupaMaterialPerProject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function dupaContentPerProject()
    {
        return $this->belongsTo(DupaContentPerProject::class);
    }
}
