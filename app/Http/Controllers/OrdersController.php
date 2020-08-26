<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\Orders\Order as OrderResource;
use App\Http\Resources\Orders\OrdersCollection;
use App\Http\Requests\Order\UpdateStatus as OrderUpdateStatusRequest;

class OrdersController extends Controller
{
    public function getOrders() {
        $orders = Order::orderBy("id", "DESC")->paginate(10);
        return (new OrdersCollection($orders))->response()->getData(true);
    }

    public function getOrder(Order $order) {
        return new OrderResource($order);
    }

    public function updateOrderStatus(Order $order, OrderUpdateStatusRequest $request) {
        $order->status = $request->status;
        $order->save();
        return new OrderResource($order);
    }
}
