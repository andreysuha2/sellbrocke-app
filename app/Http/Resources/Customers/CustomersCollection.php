<?php

namespace App\Http\Resources\Customers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Customers\Customer as CustomerResource;

class CustomersCollection extends ResourceCollection
{

    public static $wrap = "customers";
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($customer) {
            return new CustomerResource($customer);
        })->toArray();
    }
}
