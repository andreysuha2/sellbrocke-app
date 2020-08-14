<?php

namespace App\Http\Resources\Defects;

use Illuminate\Http\Resources\Json\JsonResource;

class Defect extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "defect";

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "priceReduction" => $this->price_reduction
        ];
    }
}
