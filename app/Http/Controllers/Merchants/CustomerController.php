<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function store(Request $request) {
        $merchant = Auth::guard("api-merchants")->user();
        $customer = $merchant->getCustomerByMerchantId($request->merchant_customer_id)->first();
        $data = $request->toArray();
        if(!$customer) {
            $customer = $merchant->customers()->create($data);
        } else {
            $customer->update($data);
        }
        return response()->json($customer);
    }

    public function delete(Request $request) {
        $merchant = Auth::guard("api-merchants")->user();
        $customer = $merchant->getCustomerByMerchantId($request->id)->firstOrFail();
        $customer->delete();
    }
}
