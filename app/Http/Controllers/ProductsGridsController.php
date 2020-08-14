<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductsGrids\ProductGridCollection;
use App\Models\ProductGrid;
use Illuminate\Http\Request;
use App\Http\Resources\ProductsGrids\ProductGrid as ProductGridResource;
use App\Http\Requests\ProductGrid\CreateProductGrid as CreateProductGridRequest;
use App\Http\Requests\ProductGrid\UpdateProductGrid as UpdateProductGridRequest;

class ProductsGridsController extends Controller
{
    public function getProductsGrids() {
        $productsGrids = ProductGrid::orderBy("id", "desc")->paginate(10);
        return (new ProductGridCollection($productsGrids))->response()->getData(true);
    }

    public function getProductGrid(ProductGrid $productGrid) {
        return new ProductGridResource($productGrid);
    }

    public function createProductGrid(CreateProductGridRequest $request) {
        $productGrid = ProductGrid::create($request->toArray());
        $this->attachThumbnail($productGrid, $request);
        return new ProductGridResource($productGrid);
    }

    public function updateProductGrid(ProductGrid $productGrid, UpdateProductGridRequest $request) {
        $productGrid->update($request->toArray());
        $this->attachThumbnail($productGrid, $request);
        return new ProductGridResource($productGrid);
    }

    public function deleteProductGrid(ProductGrid $productGrid, Request $request) {
        $productGrid->delete();
        $lastProductGrid = $request->lastProductGridId;
        $nextProductGrid = $lastProductGrid ? ProductGrid::orderBy("id", "desc")->where("id", "<", $lastProductGrid)->first() : null;
        $nextProductGrid = $nextProductGrid ? new ProductGridResource($nextProductGrid) : null;
        return response()->json([ "productGrid" => new ProductGridResource($productGrid), "nextProductGrid" => $nextProductGrid ]);
    }

    private function attachThumbnail(ProductGrid $productGrid, $request) {
        if($productGrid->type === "carrier" && $request->hasFile("thumbnail")) {
            $productGrid->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        } else if ($productGrid->type === "size") {
            $attachment = $productGrid->attachment("thumbnail");
            if($attachment) $attachment->delete();
        }
    }
}
