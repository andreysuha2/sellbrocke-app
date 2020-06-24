<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
        return new UserResource(Auth::user());
    }
}
