<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Condition extends JsonResource
{
    public static $wrap = "condition";
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
            "label" => $this->label,
            "description" => $this->description,
            "discountPercent" => (float) $this->discount_percent
        ];
    }
}
