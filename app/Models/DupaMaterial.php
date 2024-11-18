<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Material;
use App\Models\DupaContent;

class DupaMaterial extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'dupa_content_id',
        'material_id',
        'quantity',
    ];

    public function material() {
        return $this->belongsTo(Material::class);
    }

    public function dupaContent() {
        return $this->belongsTo(DupaContent::class);
    }
}
