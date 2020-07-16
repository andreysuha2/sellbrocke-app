<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CategoriesCollection;
use App\Models\Defect;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\CreateCategory as CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategory as UpdateCategoryRequest;
use App\Http\Resources\Category as CategoryResource;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function getRootCategories() {
        $categories = Category::where("parent_id", null)->orderBy("created_at", "desc")->get();
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
        $path->push([ "id" => $category->id, "name" => $category->name ]);
        $defects = $request->withDefects ? Defect::getList() : null;
        return response()->json([
            "category" => new CategoryResource($category, true),
            "path" => $path,
            "defects" => $defects
        ]);
    }

    public function updateCategory(Category $category, UpdateCategoryRequest $request) {
        if($request->has("slug")) {
            $this->updateDescendantsSlug($category, $request->slug);
        }
        $category->update($request->toArray());
        if($request->has("attach_defects")) $category->defects()->attach($request->attach_defects);
        if($request->has("detach_defects")) $category->defects()->detach($request->detach_defects);
        $this->uploadThumbnail($request, $category);
        return new CategoryResource($category);
    }

    public function deleteCategory(Category $category) {
        Gate::authorize("delete-category", $category);
        $category->forceDelete();
        return new CategoryResource($category);
    }

    private function uploadThumbnail($request, Category $category) {
        if($request->hasFile("thumbnail")) {
            $category->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }

    private function updateDescendantsSlug(Category $category, $slug) {
        if($category->slug !== $slug) {
            $category->descendants->each(function($descendant) use ($category, $slug) {
                $descendant->slug = Str::replaceFirst($category->slug, $slug, $descendant->slug);
                $descendant->save();
            });
        }
    }
}
