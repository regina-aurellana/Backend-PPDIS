<?php

namespace App\Models;

use App\Models\B3Projects;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barangay extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function b3Project()
    {
        return $this->hasMany(B3Projects::class, 'barangay_id', 'id');
    }
}
