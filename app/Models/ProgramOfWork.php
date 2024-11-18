<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\B3Projects;
use App\Models\PowTable;

class ProgramOfWork extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class);
    }

    public function group()
    {
        return $this->belongsTo(DupaPerProjectGroup::class, 'id');
    }

    public function powTable()
    {
        return $this->hasMany(PowTable::class, 'program_of_work_id', 'id');
    }
}
