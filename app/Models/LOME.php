<?php

namespace App\Models;

use App\Models\Material;
use App\Models\B3Projects;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LOME extends Model
{
    use HasFactory;

    protected $table = 'lome';

    protected $fillable = [
        'b3_project_id',
        'material_id',
        'quantity'
    ];

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
