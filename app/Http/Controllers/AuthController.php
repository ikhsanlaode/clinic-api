<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function login(Request $request) {
        $token = app('auth')->attempt($request->only('email', 'password'));
 
        return response()->json(['access_token' => $token,
                                'token_type' => 'bearer',
                                'expires_in' => Auth::factory()->getTTL() * 60
                                ]);
    }

    public function logout(Request $request) {
        Auth::logout();

        return response()->json('Successfully logged out');
    }
}
