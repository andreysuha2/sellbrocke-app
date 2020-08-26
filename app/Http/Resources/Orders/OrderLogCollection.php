<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Orders\OrderLog as OrderLogResource;

class OrderLogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->sortByDesc('created_at')->map(function ($orderLog) {
            return new OrderLogResource($orderLog);
        })->toArray();
    }
}
