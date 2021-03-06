<?php

namespace App\Http\Resources\OrderDevices;

use App\Http\Resources\Companies\Company as CompanyResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Conditions\Condition as ConditionResource;
use App\Http\Resources\Defects\DefectsCollection;
use App\Http\Resources\ProductsGrids\ProductGridCollection;

class OrderDevice extends JsonResource
{
    public static $wrap = "orderDevice";
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $productsGrids = $this->device->use_products_grids ? new ProductGridCollection($this->products_grids) : null;
        $name = $this->device->name;
        $thumbnailRecord = $this->device->attachment("thumbnail");
        $thumbnailPath = $thumbnailRecord ? $thumbnailRecord->url : null;

        return [
            "deviceId" => $this->device->id,
            "id" => $this->id,
            "name" => $name,
            "thumbnail" => $thumbnailPath,
            "description" => $this->device->description,
            "prices" => [
                "base" => $this->device->base_price,
                "discounted" => $this->discounted_price
            ],
            "company" => new CompanyResource($this->device->company),
            "condition" => new ConditionResource($this->condition),
            "defects" => new DefectsCollection($this->defects),
            "productsGrids" => $this->when($this->device->use_products_grids, $productsGrids)
        ];
    }
}
