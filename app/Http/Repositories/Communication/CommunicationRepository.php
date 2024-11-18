<?php

namespace App\Http\Repositories\Communication;

use App\Http\Contracts\Communication\CommunicationInterface;
use App\Http\Requests\Communication\StoreCommunicationRequest;
use App\Models\Communication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommunicationRepository 
{

    public $communication;
    
    public function __construct(CommunicationInterface $communication)
    {
        $this->communication = $communication;
    }

    /**
     * @param StoreCommunicationRequest $request
     * @return Communication
     */
    public function create(StoreCommunicationRequest $request) : Communication
    {
        $communication = $this->communication->create(User::find(2), $request);

        return $communication;
    }

}