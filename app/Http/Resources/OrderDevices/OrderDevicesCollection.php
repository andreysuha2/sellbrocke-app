<?php

namespace App\Http\Resources\OrderDevices;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\OrderDevices\OrderDevice as OrderDeviceResource;

class OrderDevicesCollection extends ResourceCollection
{
    public static $wrap = "orderDevices";
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($orderDevice) {
            return new OrderDeviceResource($orderDevice);
        })->toArray();
    }
}
