<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriesCollection;
use App\Models\Defect;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\CreateCategory as CreateCategoryRequest;
use App\Http\Resources\Category as CategoryResource;

class CategoriesController extends Controller
{
    public function getRootCategories() {
        $categories = Category::where("parent_id", null)->get();
        $defectsList = Defect::getList();
        return response()->json([ "categories" => new CategoriesCollection($categories), "defects" => $defectsList ]);
    }

    public function createCategory(CreateCategoryRequest $request, $parentId = null) {
        $category = Category::create($request->toArray());
        $category->defects()->attach($request->defects);
        $this->uploadThumbnail($request, $category);
        return new CategoryResource($category);
    }

    private function uploadThumbnail($request, Category $category) {
        if($request->hasFile("thumbnail")) {
            $category->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }
}
