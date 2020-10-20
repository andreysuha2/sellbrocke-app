<?php

namespace App\Http\Controllers;

use App\Jobs\OrderReminderNotificationJob;
use App\Models\Order;
use App\Http\Resources\Orders\Order as OrderResource;
use App\Http\Resources\Orders\OrdersCollection;
use App\Http\Requests\Order\UpdateStatus as OrderUpdateStatusRequest;
use App\Services\FedExService;
use App\Services\UPSService;
use GuzzleHttp\Client;

class OrdersController extends Controller
{
    public $fedExService;

    public function getOrders() {
        $orders = Order::orderBy("id", "DESC")->paginate(10);
        return (new OrdersCollection($orders))->response()->getData(true);
    }

    public function getOrder(Order $order) {
        if ($order->shipment->type == 'FEDEX') {
            $this->fedExService = new FedExService();
            $tracking = $this->fedExService->tracking($order->shipment->tracking_number);
        } else {
            $shipping = new UPSService(new Client());
            $tracking = $shipping->tracking($order->shipment->tracking_number);
        }

        return new OrderResource($order, $tracking);
    }

    public function updateOrderStatus(Order $order, OrderUpdateStatusRequest $request) {
        $order->status = $request->status;
        $order->save();
        return new OrderResource($order);
    }

    public function orderReminder(Order $order) {
        dispatch(new OrderReminderNotificationJob($order));
    }
}
