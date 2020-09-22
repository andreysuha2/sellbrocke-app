<?php

namespace App\Http\Resources\Customers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchants\Merchant as MerchantResource;

class Customer extends JsonResource
{

    public static $wrap = "customer";
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "ids" => [
                "app" => $this->id,
                "merchant" => $this->merchant_customer_id
            ],
            "merchant" => new MerchantResource($this->merchant, false),
            "email" => $this->email,
            "login" => $this->login,
            "firstName" => $this->first_name,
            "lastName" => $this->last_name,
            "address" => $this->address,
            "city" => $this->city,
            "state" => $this->state,
            "zip" => $this->zip,
            "phone" => $this->phone,
            "emailPayPal" => $this->paypal_email
        ];
    }
}
