<?php

namespace App\Models;

use App\Models\B3Projects;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function barangay()
    {
        return $this->hasMany(Barangay::class, 'district_id', 'id');
    }

    public function b3Project()
    {
        return $this->hasMany(B3Projects::class, 'district_id', 'id');
    }

    public function communication()
    {
        return $this->belongsTo(Communication::class, 'district_id');
    }
}
