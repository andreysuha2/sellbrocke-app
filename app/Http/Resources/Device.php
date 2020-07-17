<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Company as CompanyResource;

class Device extends JsonResource
{
    public static $wrap = "device";
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
            "name" => $this->name,
            "prices" => [
                "base" => $this->base_price,
                "discounted" => $this->getDiscounted()
            ],
            "description" => $this->descrition,
            "company" => new CompanyResource($this->company),
            "categories" => new CategoriesCollection($this->categories)
        ];
    }

    private function getDiscounted() {
        $percent = (100 - $this->company->price_reduction) / 100;
        return $this->base_price * $percent;
    }
}
