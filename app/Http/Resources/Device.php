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
                "base" => (float) $this->base_price,
                "discounted" => $this->getDiscounted()
            ],
            "thumbnail" => $thumbnailPath,
            "slug" => $this->slug,
            "description" => $this->description,
            "company" => new CompanyResource($this->company),
            "categories" => new CategoriesCollection($this->categories),
            "useProductsGrids" => (int) $this->use_products_grids,
            "productsGrids" => $this->when($this->use_products_grids, [
                "sizes" => isset($sizes) && count($sizes) ? new ProductGridCollection($sizes) : null,
                "carriers" => isset($carriers) && count($carriers) ? new ProductGridCollection($carriers) : null
            ])
        ];
    }

    private function getDiscounted() {
        $percent = (100 - $this->company->price_reduction) / 100;
        return round($this->base_price * $percent, 2);
    }
}
