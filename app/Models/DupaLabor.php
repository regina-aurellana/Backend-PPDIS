<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Labor;
use App\Models\DupaContent;

class DupaLabor extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $guarded = [];

    public function labor() {
        return $this->belongsTo(Labor::class);
    }

    public function dupaContent() {
        return $this->belongsTo(DupaContent::class);
    }
}
