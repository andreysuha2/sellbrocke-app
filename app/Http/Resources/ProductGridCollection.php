<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ProductGrid as ProductGridResource;

class ProductGridCollection extends ResourceCollection
{
    public static $wrap = "productsGrids";
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($productGrid) {
            return new ProductGridResource($productGrid);
        })->toArray();
    }
}
