<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdersCollection;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrdersController extends Controller
{
    public function getOrders(Customer $customer) {
        Gate::forUser(Auth::guard("api-merchants")->user())->authorize("get-orders", $customer);
        $orders = $customer->orders()->paginate(5);
        return (new OrdersCollection($orders))->response()->getData(true);
    }
}
