<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Communication\CommunicationRepository;
use App\Models\Communication;
use App\Http\Requests\Communication\StoreCommunicationRequest;
use App\Http\Requests\Communication\UpdateCommunicationRequest;
use Illuminate\Contracts\Container\Container;

class CommunicationController extends Controller
{

    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function index()
    {
        return 'test';
    }
    
    public function store(StoreCommunicationRequest $request)
    {
        try {
         
            $communication = $this->container->make($request->communication_category_id);
            
            $repository = app(CommunicationRepository::class, ['communication' => $communication]);
            
            $communication = $repository->create($request);

            return response($communication, 200);
            
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            return response([
                'message' => 'Invalid Communication Category'
            ], 422);
        } 
        catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }

    }
    
    public function show(Communication $communication)
    {
        //
    }
    
    public function update(UpdateCommunicationRequest $request, Communication $communication)
    {
        //
    }
    
    public function destroy(Communication $communication)
    {
        //
    }
}
