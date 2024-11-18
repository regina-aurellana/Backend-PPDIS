<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Communication;
use App\Models\DocumentReference;
use App\Models\B1ProjectIdentification;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteInspectionReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    const DRAFT = 'draft';
    const FOR_APPROVAL = 'for approval';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    public function communication()
    {
        return $this->belongsTo(Communication::class, 'communication_id', 'id');
    }

    public function b1ProjectIdentification()
    {
        return $this->hasOne(B1ProjectIdentification::class, 'site_inspection_report_id', 'id');
    }
}
