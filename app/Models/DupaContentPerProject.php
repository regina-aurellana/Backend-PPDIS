<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DupaContentPerProject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dupaPerProject()
    {
        return $this->belongsTo(DupaPerProject::class);
    }

    public function dupaEquipmentPerProject()
    {
        return $this->hasMany(DupaEquipmentPerProject::class, 'dupa_content_per_project_id');
    }

    public function dupaLaborPerProject()
    {
        return $this->hasMany(DupaLaborPerProject::class, 'dupa_content_per_project_id', 'id');
    }

    public function dupaMaterialPerProject()
    {
        return $this->hasMany(DupaMaterialPerProject::class, 'dupa_content_per_project_id', 'id');
    }
}
