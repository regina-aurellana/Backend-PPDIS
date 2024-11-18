<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Communication;
use App\Models\SiteInspectionReport;
use App\Models\ProjectNature;
use App\Models\ProjectNatureType;

class B1ProjectIdentification extends Model
{
    use HasFactory;

    protected $guarded = [];

    const DRAFT = 'draft';
    const FOR_APPROVAL = 'for approval';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    public function communication()
    {
        return $this->belongsTo(Communication::class, 'communication_id', 'id');
    }

    public function siteInspectionReport()
    {
        return $this->belongsTo(SiteInspectionReport::class, 'site_inspection_report_id', 'id');
    }

    public function projectNature()
    {
        return $this->belongsTo(ProjectNature::class, 'project_nature_id', 'id');
    }

    public function projectNatureType()
    {
        return $this->belongsTo(ProjectNatureType::class, 'project_nature_type_id', 'id');
    }
}
