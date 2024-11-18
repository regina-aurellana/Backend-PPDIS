<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProgramOfWork;
use App\Models\SowCategory;

class PowTable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function programOfWork()
    {
        return $this->belongsTo(ProgramOfWork::class, 'program_of_work_id', 'id');
    }

    public function sowCategory()
    {
        return $this->belongsTo(SowCategory::class);
    }

    public function contents()
    {
        return $this->hasMany(PowTableContent::class, 'pow_table_id', 'id');
    }
}
