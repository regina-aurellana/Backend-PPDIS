<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\DupaLabor;

class Labor extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'item_code',
        'designation',        
        'hourly_rate',
        'active',
        'group'
    ];

    public function dupaLabor(){
        return $this->hasMany(DupaLabor::class, 'labor_id', 'id');
    }
}
