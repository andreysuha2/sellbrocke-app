<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceOrder extends JsonResource
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
        $thumbnailRecord = $this->attachment("thumbnail");
        $thumbnailPath = $thumbnailRecord ? $thumbnailRecord->url : null;
        if($this->use_products_grids) {
            $sizes = $this->productsGrids->filter(function ($productGrid) { return $productGrid->type === "size"; });
            $carriers = $this->productsGrids->filter(function ($productGrid) { return $productGrid->type === "carrier"; });
        }

        return [
            "id" => $this->id,
            "name" => $this->name,
            "prices" => [
                "base" => $this->base_price,
                "discounted" => $this->getDiscounted()
            ],
            "thumbnail" => $thumbnailPath,
            "description" => $this->description,
            "useProductsGrids" => (int) $this->use_products_grids,
            "productsGrids" => $this->when($this->use_products_grids, [
                "sizes" => isset($sizes) && count($sizes) ? new ProductGridCollection($sizes) : null,
                "carriers" => isset($carriers) && count($carriers) ? new ProductGridCollection($carriers) : null
            ]),
            "defects" => new DefectsCollection($this->defects)
        ];
    }

    private function getDiscounted() {
        $percentCompany = (100 - $this->company->price_reduction) / 100;
        $percentDefects = (100 - collect($this->defects)->sum("price_reduction")) / 100;
        return [
            "company" => round($this->base_price * $percentCompany, 2),
            "defects" => round($this->base_price * $percentDefects, 2)
        ];
    }
}
