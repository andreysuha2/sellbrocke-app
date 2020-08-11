<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\Order as OrderResource;

class OrdersController extends Controller
{
    public function getOrders() {
        $orders = Order::all();
        return response()->json($orders);
    }

    public function getOrder(Order $order) {
        return new OrderResource($order);
    }
}
