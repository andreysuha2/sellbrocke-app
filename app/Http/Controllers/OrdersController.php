<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\OrdersCollection;

class OrdersController extends Controller
{
    public function getOrders() {
        $orders = Order::paginate(10);
        return (new OrdersCollection($orders))->response()->getData(true);
    }

    public function getOrder(Order $order) {
        return new OrderResource($order);
    }
}
