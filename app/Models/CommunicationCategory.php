<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function communication()
    {
        return $this->belongsTo(Communication::class, 'comms_category_id');
    }
}
