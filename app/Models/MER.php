<?php

namespace App\Models;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MER extends Model
{
    use HasFactory;
    protected $table = 'mer';

    protected $fillable = [
        'b3_project_id',
        'equipment_id',
        'quantity'
    ];

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
