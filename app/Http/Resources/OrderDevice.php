<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Condition as ConditionResource;

class OrderDevice extends JsonResource
{
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
        if($productsGrids) {
            $size = $productsGrids->firstWhere("type", "size");
            $carrier = $productsGrids->firstWhere("type", "carrier");
            $name = $name . " " . $size->name . " (" . $carrier->name . ")";
        }
        $thumbnailRecord = $this->device->attachment("thumbnail");
        $thumbnailPath = $thumbnailRecord ? $thumbnailRecord->url : null;

        return [
            "id" => $this->device->id,
            "name" => $name,
            "thumbnail" => $thumbnailPath,
            "description" => $this->device->description,
            "prices" => [
                "base" => $this->device->base_price,
                "discounted" => $this->discounted_price
            ],
            "condition" => new ConditionResource($this->condition),
            "defects" => new DefectsCollection($this->defects),
            "productsGrids" => $this->when($this->device->use_products_grids, $productsGrids)
        ];
    }
}
