<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderShipment extends JsonResource
{
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
            "trackingNumber" => $this->tracking_number,
            "type" => $this->type,
            "currency" => [
                "total" => $this->total_charges,
                "code" => $this->currency_code
            ],
            "weight" => [
                "value" => $this->weight,
                "code" => $this->weight_code
            ],
            "label" => $this->attachment("label")->url
        ];
    }
}
