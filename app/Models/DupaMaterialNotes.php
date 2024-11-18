<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Dupa;

class DupaMaterialNotes extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function dupa(){
        return $this->belongsTo(Dupa::class);
    }
}
