<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ABC;
use App\Models\PowTableContent;

class ABCContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'abc_id',
        'pow_table_content_id',
        'total_cost',
    ];

    public function abc()
    {
        return $this->belongsTo(ABC::class);
    }

    public function powTable()
    {
        return $this->hasOne(PowTableContent::class, 'pow_table_content_id', 'id');
    }
}
