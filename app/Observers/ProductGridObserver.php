<?php

namespace App\Observers;
use App\Models\ProductGrid;

class ProductGridObserver
{
    public function forceDeleted(ProductGrid $productGrid) {
        $productGrid->devices()->detach();
    }
}
