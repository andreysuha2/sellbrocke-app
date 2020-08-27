<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customers\Customer as CustomerResource;
use App\Http\Resources\OrderDevices\OrderDevicesCollection;
use App\Http\Resources\Orders\OrderShipment as OrderShipmentResource;

class Order extends JsonResource
{
    public static $wrap = "order";
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "devices" => new OrderDevicesCollection($this->devices),
            "customer" => new CustomerResource($this->customer),
            "prices" => $this->prices,
            "date" => $this->created_at,
            "log" => new OrderLogCollection($this->log),
            "shipment" => new OrderShipmentResource($this->shipment)
        ];
    }
}
