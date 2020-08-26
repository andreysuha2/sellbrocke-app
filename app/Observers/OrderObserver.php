<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order) {
        $order->log()->create([ "message" => "Order created with number: $order->id" ]);
    }

    public function updated(Order $order) {
        if($order->status !== $order->getOriginal("status")) {
            $oldStatus = $order->getOriginal("status");
            $order->log()->create([ "message" => "Status change: '$oldStatus' -> '$order->status'" ]);
        }
    }
}
