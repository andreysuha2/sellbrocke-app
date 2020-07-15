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
        if($parentId) {
            $parent = Category::findOrFail($parentId);
            $category = $parent->children()->create($request->toArray());
        } else $category = Category::create($request->toArray());
        $category->defects()->attach($request->defects);
        $this->uploadThumbnail($request, $category);
        return new CategoryResource($category);
    }

    public function getCategory(Category $category, Request $request) {
        $path = $category->ancestors()->select("id", "name")->get();
        $defects = $request->withDefects ? Defect::getList() : null;
        return response()->json([
            "category" => new CategoryResource($category, true),
            "path" => $path,
            "defects" => $defects
        ]);
    }

    private function uploadThumbnail($request, Category $category) {
        if($request->hasFile("thumbnail")) {
            $category->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }
}
