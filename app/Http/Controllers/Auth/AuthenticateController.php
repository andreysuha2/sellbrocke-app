<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modes\User;
use App\Http\Resources\User as UserResource;

class AuthenticateController extends Controller
{
    public function login(Request $request) {
        $user = User::where("email", $request->email)->first();
        if(!$user) return response()->json([ "message" => "Invalid credentials" ], 422);
        if(Hash::check($request->password, $user->password)) {
            $token = $user->createToken("Admin client $user->name")->accessToken;
            Auth::login($user);
            return (new UserResource($user))->additional([ "token" => $token ]);
        } else return response()->json([ "message" => "Invalid credentials" ], 422);
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json("OK", 200);
    }
}
