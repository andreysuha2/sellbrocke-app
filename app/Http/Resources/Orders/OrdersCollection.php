<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Orders\Order as OrderResource;

class OrdersCollection extends ResourceCollection
{
    public static $wrap = "orders";
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($order) {
            return new OrderResource($order);
        })->toArray();
    }
}
