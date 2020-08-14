<?php

namespace App\Http\Resources\Devices;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Devices\Device as DeviceResource;

class DevicesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($device) {
            return new DeviceResource($device);
        })->toArray();
    }
}
