<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Dupa;

class PowTableContentDupa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function content()
    {
        return $this->belongsTo(PowTableContent::class, 'pow_table_content_id', 'id');
    }

    public function dupaPerProject()
    {
        return $this->hasOne(DupaPerProject::class, 'id', 'dupa_per_project_id');

    }
}
