<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersPageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($order) {
            return [
                "id" => $order->id,
                "status" => $order->status,
                "devicesCount" => $order->devices()->count(),
                "prices" => $order->prices,
                "date" => $order->created_at
            ];
        })->toArray();
    }
}
