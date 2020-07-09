<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Company extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "company";

    public function toArray($request)
    {
        $logoRecord = $this->attachment("logo");
        $logoPath = $logoRecord ? $logoRecord->url : null;
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "priceReduction" => (float) $this->price_reduction,
            "logo" => $logoPath
        ];
    }
}
