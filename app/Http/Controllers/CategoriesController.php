<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriesCollection;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\CreateCategory as CreateCategoryRequest;

class CategoriesController extends Controller
{
    public function getRootCategories() {
        $categories = Category::where("parent_id", null)->get();
        return new CategoriesCollection($categories);
    }

    public function createCategory(CreateCategoryRequest $request, $parentId = null) {
        return response()->json([ "req" => $request->toArray(), "parent_id" => $parentId ]);
    }
}
