<?php

namespace App\Http\Contracts\Attachment;

use App\Models\Communication;
use App\Models\User;

interface CommunicationHasAttachment 
{
    /**
     * @param Communication $communication
     * @param User $user
     * @return string
     */
    public function storeReferenceDocuments(Communication $communication, User $user, $request) : string;

    public function generateReferenceNumber() : string;

}