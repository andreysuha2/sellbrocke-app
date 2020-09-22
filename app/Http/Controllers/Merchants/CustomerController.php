<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index($merchantCustomerId) {
        $merchant = Auth::guard("api-merchants")->user();
        $customer = $merchant->getCustomerByMCId($merchantCustomerId)->first();

        return response()->json($customer);
    }

    public function store(Request $request) {
        $merchant = Auth::guard("api-merchants")->user();
        $customer = $merchant->getCustomerByMCId($request->merchant_customer_id)->first();
        $data = $request->toArray();
        if(!$customer) {
            $customer = $merchant->customers()->create($data);
        } else {
            $customer->update($data);
        }
        return response()->json($customer);
    }

    public function update(Request $request) {
        $merchant = Auth::guard("api-merchants")->user();
        $customer = $merchant->getCustomerByMCId($request->merchant_customer_id)->first();

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->zip = $request->zip;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->paypal_email = $request->paypal_email;
        $customer->save();

        return response()->json($customer);
    }

    public function delete(Request $request) {
        $merchant = Auth::guard("api-merchants")->user();
        $customer = $merchant->getCustomerByMCId($request->id)->firstOrFail();
        $customer->delete();
    }
}
