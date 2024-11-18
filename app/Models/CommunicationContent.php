<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationContent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function communication()
    {
        return $this->belongsTo(Communication::class, 'communication_id', 'id');
    }

    public function routedToUser()
    {
        return $this->belongsTo(User::class, 'route_to_user_id', 'id');
    }

    public function routedByUser()
    {
        return $this->belongsTo(User::class, 'route_by_user_id', 'id');
    }
}
