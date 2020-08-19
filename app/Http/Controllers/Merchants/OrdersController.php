<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\Orders\OrdersPageCollection;
use App\Models\Condition;
use App\Models\Device;
use App\Models\OrderDevice;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use App\Http\Resources\Orders\Order as OrderResource;
use App\Http\Requests\Order\CreateOrder as CreateOrderRequest;

class OrdersController extends Controller
{
    public function getOrders(Customer $customer) {
        $orders = $customer->orders()->paginate(10);
        return (new OrdersPageCollection($orders))->response()->getData(true);
    }

    public function getOrder(Customer $customer, Order $order) {
        Gate::forUser(Auth::guard("api-merchants")->user())->authorize("get-order", [ $customer, $order ]);
        return new OrderResource($order);
    }

    public function createOrder(Customer $customer, CreateOrderRequest $request) {
        Gate::forUser(Auth::guard("api-merchants")->user())->authorize("create-order", $customer);
        $order = $customer->orders()->create([ "status" => "open" ]);
        collect($request->devices)->each(function ($deviceData) use ($order) {
            $orderDevice = new OrderDevice();
            $device = Device::find($deviceData["id"]);
            $condition = Condition::find($deviceData["condition"]);
            $orderDevice->order()->associate($order);
            $orderDevice->device()->associate($device);
            $orderDevice->condition()->associate($condition);
            $orderDevice->save();
            if(isset($deviceData["defects"])) $orderDevice->defects()->attach($deviceData["defects"]);
            if($device->use_products_grids && isset($deviceData["productsGrids"])) {
                $orderDevice->products_grids()->attach($deviceData["productsGrids"]);
            }
        });
        return new OrderResource($order);
    }
}
