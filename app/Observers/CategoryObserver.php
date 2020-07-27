<?php

namespace App\Observers;
use App\Models\Category;

class CategoryObserver
{
    public function forceDeleted(Category $category) {
        $category->defects()->detach();
    }
}
