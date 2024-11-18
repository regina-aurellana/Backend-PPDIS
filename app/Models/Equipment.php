<?php

namespace App\Models;

use App\Models\MER;
use App\Models\DupaEquipment;
use App\Models\EquipmentComponent;

use App\Models\DupaEquipmentPerProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'item_code',
        'name',
        'hourly_rate',
        'active',
        'group'
    ];

    public function dupaEquipment()
    {
        return $this->hasMany(DupaEquipment::class, 'equipment_id', 'id');
    }

    public function dupaEquipmentPerProject()
    {
        return $this->hasMany(DupaEquipmentPerProject::class, 'equipment_id', 'id');
    }

    public function equipmentComponent()
    {
        return $this->hasMany(EquipmentComponent::class, 'equip_id', 'id');
    }

    public function mer()
    {
        return $this->hasMany(MER::class, 'equipment_id', 'id');
    }
}
