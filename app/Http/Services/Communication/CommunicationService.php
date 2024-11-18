<?php

namespace App\Http\Services\Communication;

use App\Enums\CommunicationStatusEnum;
use App\Http\Contracts\Attachment\CommunicationHasAttachment;
use App\Http\Contracts\Communication\CommunicationInterface;
use App\Http\Services\Attachment\AttachmentService;
use App\Models\Communication;
use Illuminate\Support\Facades\DB;

class CommunicationService extends AttachmentService implements CommunicationInterface, CommunicationHasAttachment
{
    
    public function create($user, $request) : ?Communication {

        $communication = null;

        DB::transaction(function () use(&$communication, $request, $user) {
           
            $communication = Communication::create([
                'comms_ref_no' => $request->communication_reference_number,
                'communication_category_id' => $request->communication_category_id,
                'district_id' => $request->district_id,
                'barangay_id' => $request->barangay_id,
                'subject' => $request->subject,
                'status' => CommunicationStatusEnum::OPEN
            ]);

            $communication->contents()->create([
                'routed_by_user_id' => $user->id,
                'routed_to_user_id' => $request->routed_to_user_id,
                'remarks' => $request->remarks,
                'action_taken' => $request->action_taken
            ]);

            $this->storeCommunicationAttachment($communication, $user, $request);

        });

        return $communication;
    }

}