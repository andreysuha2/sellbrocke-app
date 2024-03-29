<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customers\Customer as CustomerResource;
use App\Http\Resources\OrderDevices\OrderDevicesCollection;
use App\Http\Resources\Orders\OrderShipment as OrderShipmentResource;

class Order extends JsonResource
{
    public static $wrap = "order";
    public $tracking;

    public function __construct($resource, $tracking = null)
    {
        parent::__construct($resource);
        $this->tracking = $tracking;
    }

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
            "order_customer" => [
                "email" => $this->customer['email'],
                "first_name" => $this->first_name,
                "last_name" => $this->last_name,
                "address" => $this->address,
                "city" => $this->city,
                "state" => $this->state,
                "zip" => $this->zip,
                "phone" => $this->phone,
                "paypal_email" => $this->paypal_email
            ],
            "prices" => $this->prices,
            "payment" => $this->payment,
            "date" => $this->created_at->format('m-d-Y g:i a'),
            "log" => new OrderLogCollection($this->log),
            "shipment" => new OrderShipmentResource($this->shipment),
            'tracking' => $this->tracking
        ];
    }
}
