<?php

namespace App\Http\Services\DupaPerProject\LOPE;

use App\Models\LOPE;

class LOPEService
{

    public function index($b3_project)
    {
        return LOPE::where('b3_project_id', $b3_project->id)->paginate(10);
    }

    public function store($request, $b3_project)
    {
        LOPE::create([
            'b3_project_id' => $b3_project->id,
            'number' => $request->number,
            'key_personnel' => $request->key_personnel,
            'quantity' => $request->quantity
        ]);
    }

    public function update($request, $lope)
    {
        $lope->update([
            'number' => $request->number,
            'key_personnel' => $request->key_personnel,
            'quantity' => $request->quantity
        ]);
    }

    public function destroy($lope)
    {
        $lope->delete();
    }

}