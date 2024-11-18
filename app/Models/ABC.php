<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\B3Projects;
use App\Models\ABCContent;

class ABC extends Model
{
    use HasFactory;

    protected $fillable = ['b3_project_id', 'dupa_per_project_group_id'];

    public function b3Project()
    {
        return $this->belongsTo(B3Projects::class);
    }

    public function group(){
        return $this->belongsTo(DupaPerProjectGroup::class, 'id');
    }

    public function abcContent()
    {
        return $this->hasMany(ABCContent::class, 'abc_id');
    }
}
