<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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

        return [
            "id" => $this->id,
            "condition" => $this->condition,
            "defects" => new DefectsCollection($this->defects),
            "productsGrids" => $this->when($this->device->use_products_grids, $productsGrids)
        ];
    }
}
