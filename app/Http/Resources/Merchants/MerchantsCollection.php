<?php

namespace App\Http\Resources\Merchants;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Merchants\Merchant as MerchantResource;

class MerchantsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "merchants";

    public function toArray($request)
    {
        return $this->collection->map(function ($merchant) {
            return new MerchantResource($merchant, false);
        })->toArray();
    }
}
