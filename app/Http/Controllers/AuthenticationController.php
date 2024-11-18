<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    
    public function login(LoginRequest $request){

        if (Auth::guard()->attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();

            return response([
                'user' => Auth::user(),
            ], 201);
        }

        return response()->json(['error' => 'Invalid credentials']);
        

    }

    public function logout(Request $request){

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([], 204);
    }
}
