<?php

namespace App\Http\Controllers\Merchants\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Resources\MerchantsCollection;

class MerchantController extends Controller
{
    public function index() {
        $merchants = Merchant::all();
        return new MerchantsCollection($merchants);
    }
}
