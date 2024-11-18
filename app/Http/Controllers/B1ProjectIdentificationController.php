<?php

namespace App\Http\Controllers;

use App\Http\Requests\B1ProjectIdentification\AddB1ProjectIdentificationRequest;
use App\Models\B1ProjectIdentification;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class B1ProjectIdentificationController extends Controller
{
    public function index()
    {
        $project_identification = B1ProjectIdentification::with([
            'communication' => function ($q) {
                $q->select('id', 'comms_ref_no', 'communication_category_id', 'district_id', 'barangay_id', 'subject', 'location', 'status');
            },
            'siteInspectionReport' => function ($q) {
                $q->select('id', 'communication_id', 'sir_no', 'project_title', 'project_location', 'findings', 'recommendation', 'status');
            },
            'projectNature' => function ($q) {
                $q->select('id', 'name');
            },
            'projectNatureType' => function ($q) {
                $q->select('id', 'name');
            }
        ])
            ->get();

        return response()->json($project_identification);
    }

    public function create()
    {
        //
    }

    public function draftOrSubmitProjectIdentification(AddB1ProjectIdentificationRequest $request, $status)
    {
        try {
            $project_identification_no = Uuid::uuid4();

            if ($request['id'] == null) {
                B1ProjectIdentification::create([
                    'site_inspection_report_id' => $request['site_inspection_report_id'],
                    'communication_id' => $request['communication_id'],
                    'project_identification_no' => $project_identification_no,
                    'initial_project_name' => $request['initial_project_name'],
                    'address' => $request['address'],
                    'requesting_party' => $request['requesting_party'],
                    'project_nature_id' => $request['project_nature_id'],
                    'project_nature_type_id' => $request['project_nature_type_id'],
                    'reason' => $request['reason'],
                    'existing_condition' => $request['existing_condition'],
                    'estimated_beneficiary' => $request['estimated_beneficiary'],
                    'recommendation' => $request['recommendation'],
                    'contact_no' => $request['contact_no'],
                    'status' => $status,
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'B1 Project Identification Successfully ' . ($status == B1ProjectIdentification::DRAFT ? 'Saved as Draft' : 'Submitted')
                ]);
            } else {
                B1ProjectIdentification::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'site_inspection_report_id' => $request['site_inspection_report_id'],
                        'communication_id' => $request['communication_id'],
                        // 'project_identification_no' => $project_identification_no,
                        'initial_project_name' => $request['initial_project_name'],
                        'address' => $request['address'],
                        'requesting_party' => $request['requesting_party'],
                        'project_nature_id' => $request['project_nature_id'],
                        'project_nature_type_id' => $request['project_nature_type_id'],
                        'reason' => $request['reason'],
                        'existing_condition' => $request['existing_condition'],
                        'estimated_beneficiary' => $request['estimated_beneficiary'],
                        'recommendation' => $request['recommendation'],
                        'contact_no' => $request['contact_no'],
                        'status' => ($status == B1ProjectIdentification::DRAFT ? B1ProjectIdentification::DRAFT : B1ProjectIdentification::FOR_APPROVAL)
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'B1 Project Identification Successfully Updated and ' . ($status == B1ProjectIdentification::DRAFT ? 'Saved as Draft' : 'Submitted')
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function saveAsDraft(AddB1ProjectIdentificationRequest $request)
    {
        return $this->draftOrSubmitProjectIdentification($request, B1ProjectIdentification::DRAFT);
    }

    public function submit(AddB1ProjectIdentificationRequest $request)
    {
        return $this->draftOrSubmitProjectIdentification($request, B1ProjectIdentification::FOR_APPROVAL);
    }

    public function show(B1ProjectIdentification $b1_project_identification)
    {
        try {
            $content = B1ProjectIdentification::where('id', $b1_project_identification->id)
                ->with([
                    'communication' => function ($q) {
                        $q->select('id', 'comms_ref_no', 'communication_category_id', 'district_id', 'barangay_id', 'subject', 'location', 'status');
                    },
                    'siteInspectionReport' => function ($q) {
                        $q->select('id', 'communication_id', 'sir_no', 'project_title', 'project_location', 'findings', 'recommendation', 'status');
                    },
                    'projectNature' => function ($q) {
                        $q->select('id', 'name');
                    },
                    'projectNatureType' => function ($q) {
                        $q->select('id', 'name');
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

    public function destroy(B1ProjectIdentification $b1_project_identification)
    {
        try {
            $b1_project_identification->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'B1 Project Identification Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
