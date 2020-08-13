<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer as CustomerResource;

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
            "date" => $this->created_at
        ];
    }
}
