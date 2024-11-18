<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\B3Projects;

class LOPE extends Model
{
    use HasFactory;

    protected $table = 'lope';

    protected $fillable = [
        'b3_project_id',
        'number',
        'key_personnel',
        'quantity'
    ];

    public function b3Project(){
        return $this->belongsTo(B3Projects::class);
    }
}
