<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Dupa;

class CategoryDupa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dupas()
    {
        return $this->hasMany(Dupa::class, 'category_dupa_id', 'id');
    }
}
