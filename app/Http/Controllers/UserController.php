<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\AddUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['team', 'role'])->get();

        return response()->json($users);
    }

    public function create()
    {
        //
    }

    public function store(AddUserRequest $request)
    {
        try {
            if ($request['id'] == null) {

                $user = User::create([
                    'name' => $request['name'],
                    'username' => $request['username'],
                    'position' => $request['position'],
                    'password' => bcrypt($request['password']),
                    'is_active' => true,
                    'role_id' => $request['role_id'],
                    'team_id' => $request['team_id'] ?? null
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'User Successfully Created'
                ]);
            } else {
                $user = User::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'name' => $request['name'],
                        'username' => $request['username'],
                        'password' => bcrypt($request['password']),
                        'position' => $request['position'],
                        'is_active' => $request['is_active'],
                        'role_id' => $request['role_id'],
                        'team_id' => $request['team_id'] ?? null
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'User Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function show(User $user)
    {
        try {
            $content = User::where('id', $user->id)
                ->with(['team', 'role'])
                ->first();

            return response()->json($content);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function edit(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
    }

    public function destroy(User $user)
    {
        try {
            if (auth()->user()->id == $user->id) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Cannot remove Current User.'
                ]);
            }
            $user->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'User Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }
}
