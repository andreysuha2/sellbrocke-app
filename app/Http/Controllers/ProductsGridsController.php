<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductGridCollection;
use App\Models\ProductGrid;
use Illuminate\Http\Request;
use App\Http\Resources\ProductGrid as ProductGridResource;
use App\Http\Requests\ProductGrid\CreateProductGrid as CreateProductGridRequest;
use App\Http\Requests\ProductGrid\UpdateProductGrid as UpdateProductGridRequest;

class ProductsGridsController extends Controller
{
    public function getProductsGrids() {
        $productsGrids = ProductGrid::paginate(10);
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

    public function deleteProductGrid(ProductGrid $productGrid) {
        $productGrid->delete();
        return new ProductGridResource($productGrid);
    }

    private function attachThumbnail(ProductGrid $productGrid, $request) {
        if($productGrid->type === "carrier" && $request->hasFile("thumbnail")) {
            $productGrid->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }
}
