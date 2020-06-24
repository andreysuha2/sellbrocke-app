<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modes\User;

class AuthenticateController extends Controller
{
    public function login(Request $request) {
        $user = User::where("email", $request->email)->firstOrFail();
        if(Hash::check($request->password, $user->password)) {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            Auth::login($user);
            return response()->json(["user" => $user, "token" => $token], 200);
        } else return response()->json([ "msg" => "Invalid credentials" ], 422);
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json("OK", 200);
    }
}
