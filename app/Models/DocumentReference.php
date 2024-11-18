<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Communication;

class DocumentReference extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function communication()
    {
        return $this->belongsTo(Communication::class, 'communication_id', 'id');
    }
}
