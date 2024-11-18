<?php

namespace App\Http\Contracts\Communication;

use App\Http\Requests\Communication\StoreCommunicationRequest;
use App\Models\Communication;
use App\Models\User;

interface CommunicationInterface 
{
    /**
     * @param User $user
     * @param StoreCommunicationRequest $request
     * @return Communication
     */
    public function create(User $user, StoreCommunicationRequest $request) : ?Communication;

}