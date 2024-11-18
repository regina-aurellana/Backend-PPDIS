<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\DupaEquipment;
use App\Models\DupaLabor;
use App\Models\DupaMaterial;
use App\Models\Dupa;

class DupaContent extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dupa_id',

    ];

    public function dupaEquipment() {
        return $this->hasMany(DupaEquipment::class, 'dupa_content_id');
    }
    public function dupaLabor() {
        return $this->hasMany(DupaLabor::class, 'dupa_content_id', 'id');
    }
    public function dupaMaterial() {
        return $this->hasMany(DupaMaterial::class, 'dupa_content_id', 'id');
    }
    public function dupa() {
        return $this->belongsTo(Dupa::class);
    }
}
