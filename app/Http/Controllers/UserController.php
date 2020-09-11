<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateUser as UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        return new UserResource(Auth::user());
    }

    public function update(UpdateUserRequest $request) {
        $user = Auth::user();
        $user->update($request->toArray());
        if($request->has("new_password")) {
            $user->password = Hash::make($request->new_password);
            $user->save();
        }
        return new UserResource($user);
    }
}
