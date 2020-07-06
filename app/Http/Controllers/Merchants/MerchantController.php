<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Merchant as MerchantResource;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function index() {
        return new MerchantResource(Auth::guard('api-merchants')->user());
    }
}
