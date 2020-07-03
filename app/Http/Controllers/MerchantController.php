<?php

namespace App\Http\Controllers;

use  Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function index()
    {
        return response()->json(Auth::guard('api-merchants')->user());
    }
}
