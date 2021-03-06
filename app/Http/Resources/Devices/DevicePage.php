<?php

namespace App\Http\Resources\Devices;

use App\Http\Resources\ProductsGrids\ProductGrid as ProductGridResource;
use App\Http\Resources\ProductsGrids\ProductGridCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DevicePage extends JsonResource
{

    private $size = null;
    private $carrier = null;
    private $withProductsGrids = false;

    public function __construct($resource, $size = null, $carrier = null) {
        parent::__construct($resource);
        $this->carrier = $carrier;
        $this->size =$size;
        if($this->use_products_grids) {
            $this->withProductsGrids = (boolean) ($size && $carrier);
            $this->carrier = $this->productsGrids()->where("type", "carrier")->where("slug", $carrier)->first();
            $this->size = $this->productsGrids()->where("type", "size")->where("slug", $size)->first();
            if($size && $carrier && (!$this->carrier || !$this->size)) abort(404);
        }
    }

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
        if($this->withProductsGrids) {
            $this->size = new ProductGridResource($this->size);
            $this->carrier = new ProductGridResource($this->carrier);
        }

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
            "company" => $this->company,
            "mainSlug" => $this->categories[0]->slug . "/" . $this->company->slug . "/" . $this->slug,
            "description" => $this->description,
            "productsGrids" => $this->when($this->withProductsGrids, [ "size" => $this->size, "carrier" => $this->carrier ]),
            "productsGridsList" => $this->when($this->use_products_grids, [
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
