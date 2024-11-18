<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\TakeOffTableFields;
use App\Models\TakeOffTable;

class TakeOffTableFieldsInput extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'row_no' => 'integer',
    ];

    public function takeOffTableField(){
        return $this->belongsTo(TakeOffTableFields::class, 'take_off_table_field_id');
    }

    public function takeOffTable(){
        return $this->belongsTo(TakeOffTable::class, 'take_off_table_id', 'id');
    }
}
