<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\CommunicationContent;
use App\Models\DocumentReference;
use App\Models\CommunicationCategory;
use App\Models\District;
use App\Models\Barangay;

class Communication extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(CommunicationContent::class, 'communication_id', 'id');
    }

    public function referenceDocuments()
    {
        return $this->hasMany(DocumentReference::class, 'communication_id', 'id');
    }

    public function communicationCategory()
    {
        return $this->belongsTo(CommunicationCategory::class, 'communication_category_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }
}
