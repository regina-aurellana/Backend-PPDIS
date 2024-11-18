<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\AddRoleRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::get();

        return response()->json($roles);
    }

    public function create()
    {
        //
    }

    public function store(AddRoleRequest $request)
    {
        try {
            if ($request['id'] == null) {
                Role::create([
                    'name' => $request['name'],
                ]);

                return response()->json([
                    'status' => 'Created',
                    'message' => 'Role Successfully Created'
                ]);
            } else {
                Role::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'name' => $request['name'],
                    ]
                );

                return response()->json([
                    'status' => 'Updated',
                    'message' => 'Role Successfully Updated'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function show(Role $role)
    {
        try {
            $content = Role::where('id', $role->id)->first();

            return response()->json($content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }

    public function edit(Role $role)
    {
        //
    }

    public function update(Request $request, Role $role)
    {
        //
    }

    public function destroy(Role $role)
    {
        try {
            if ( User::whereHas('role', function($q) use($role) {
                $q->where('id', $role->id);
            })->first()) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Cannot remove Role. There are users that are currently assigned to this role.'
                ]);
            }

            $role->delete();

            return response()->json([
                'status' => 'Success',
                'message' => 'Role Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
