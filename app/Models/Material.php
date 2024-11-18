<?php

namespace App\Models;

use App\Models\LOME;
use App\Models\DupaMaterial;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'item_code',
        'name',
        'unit',
        'unit_cost',
        'active',
        'group'
    ];

    public function dupaMaterial()
    {
        return $this->hasMany(DupaMaterial::class, 'material_id', 'id');
    }

    public function lome()
    {
        return $this->hasMany(LOME::class, 'material_id', 'id');
    }
}
