<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Team\AddTeamRequest;

class TeamController extends Controller
{

    public function index()
    {
        $teams = Team::get();

        return response()->json($teams);
    }

    public function create()
    {
        
    }

    public function store(AddTeamRequest $request)
    {
        try {
            if ($request['id'] == null) {
                $team = Team::latest()->first();

                if($team){
                    Team::create([
                        'name' => $team->name + 1 ,
                    ]);
                }else {
                    Team::create([
                        'name' => 1,
                    ]);
                }

                return response()->json([
                    'status' => 'Created',
                    'message' => 'Team Successfully Created'
                ]);
            } else {
                Team::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'name' => $request['name'],
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'Team Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(Team $team)
    {
        try {
            if ( User::whereHas('team', function($q) use($team) {
                $q->where('id', $team->id);
            })->first()) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Cannot remove Team. There are users that are currently assigned to this team.'
                ]);
            }

            $team->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Team Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
