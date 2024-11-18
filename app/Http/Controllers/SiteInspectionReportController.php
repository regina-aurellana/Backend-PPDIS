<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteInspectionReport\AddSiteInspectionReportRequest;
use App\Models\SiteInspectionReport;
use Illuminate\Http\Request;

class SiteInspectionReportController extends Controller
{
    public function index()
    {
        $sir = SiteInspectionReport::with([
            'communication' => function ($q) {
                $q->select('id', 'comms_ref_no', 'communication_category_id', 'district_id', 'barangay_id', 'subject', 'location', 'status');
            },
            'b1ProjectIdentification' => function ($q) {
                $q->select('id', 'site_inspection_report_id', 'communication_id', 'b1_id_no', 'initial_project_name', 'address', 'requesting_party', 'project_nature_id', 'project_nature_type_id', 'reason', 'existing_condition', 'estimated_beneficiary', 'recommendation', 'contact_no', 'status');
            }
        ])
            ->get();

        return response()->json($sir);
    }

    public function create()
    {
        //
    }

    public function generateSIRNo()
    {
        $all_sir_no = SiteInspectionReport::withTrashed()->latest()->first();
        $last_sir_no = $all_sir_no ? $all_sir_no->sir_no : null;
        $next_sir_no = $last_sir_no ? intval(substr($last_sir_no, 4)) + 1 : 1;
        return 'PDD-' . str_pad($next_sir_no, 3, '0', STR_PAD_LEFT);
    }

    public function draftOrSubmitSiteInspectionReport(AddSiteInspectionReportRequest $request, $status)
    {
        try {
            $sir_no = $this->generateSIRNo();

            if ($request['id'] == null) {
                SiteInspectionReport::create([
                    'communication_id' => $request['communication_id'],
                    'sir_no' => $sir_no,
                    'project_title' => $request['project_title'],
                    'project_location' => $request['project_location'],
                    'findings' => $request['findings'],
                    'recommendation' => $request['recommendation'],
                    'status' => $status,
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'Site Inspection Report Successfully ' . ($status == SiteInspectionReport::DRAFT ? 'Saved as Draft' : 'Submitted')
                ]);
            } else {
                SiteInspectionReport::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'communication_id' => $request['communication_id'],
                        'project_title' => $request['project_title'],
                        'project_location' => $request['project_location'],
                        'findings' => $request['findings'],
                        'recommendation' => $request['recommendation'],
                        'status' => ($status == SiteInspectionReport::DRAFT ? SiteInspectionReport::DRAFT : SiteInspectionReport::FOR_APPROVAL)
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'Site Inspection Report Successfully Updated and ' . ($status == SiteInspectionReport::DRAFT ? 'Saved as Draft' : 'Submitted')
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function saveAsDraft(AddSiteInspectionReportRequest $request)
    {
        return $this->draftOrSubmitSiteInspectionReport($request, SiteInspectionReport::DRAFT);
    }

    public function submit(AddSiteInspectionReportRequest $request)
    {
        return $this->draftOrSubmitSiteInspectionReport($request, SiteInspectionReport::FOR_APPROVAL);
    }

    public function show(SiteInspectionReport $site_inspection_report)
    {
        try {
            $content = SiteInspectionReport::where('id', $site_inspection_report->id)
                ->with([
                    'communication' => function ($q) {
                        $q->select('id', 'comms_ref_no', 'communication_category_id', 'district_id', 'barangay_id', 'subject', 'location', 'status');
                    },
                    'b1ProjectIdentification' => function ($q) {
                        $q->select('id', 'site_inspection_report_id', 'communication_id', 'b1_id_no', 'initial_project_name', 'address', 'requesting_party', 'project_nature_id', 'project_nature_type_id', 'reason', 'existing_condition', 'estimated_beneficiary', 'recommendation', 'contact_no', 'status');
                    }
                ])
                ->first();

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(SiteInspectionReport $site_inspection_report)
    {
        try {
            if ($site_inspection_report->b1ProjectIdentification)
                $site_inspection_report->b1ProjectIdentification->delete();

            $site_inspection_report->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Site Inspection Report Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
