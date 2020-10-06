<?php

namespace App\Http\Controllers\Merchants\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Resources\Merchants\MerchantsCollection;
use App\Http\Requests\Merchant\UpdatePassword as UpdatePasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
{
    public function index() {
        $merchants = Merchant::all();
        return new MerchantsCollection($merchants);
    }

    public function updatePassword(Merchant $merchant, UpdatePasswordRequest $request) {
        $merchant->password = Hash::make($request->new_password);
        $merchant->save();
        DB::table("oauth_access_tokens")
            ->where("user_id", $merchant->id)
            ->where("client_id", 2)
            ->update([ "revoked" => true ]);
        return response()->json([ "success" => "OK" ]);
    }
}
