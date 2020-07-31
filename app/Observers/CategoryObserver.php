<?php

namespace App\Observers;
use App\Models\Category;
use App\Models\SearchSlug;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function forceDeleted(Category $category) {
        $category->defects()->detach();
        $category->searchSlugs()->delete();
    }

    public function created(Category $category) {
        $category->searchSlugs()->create([ "category_part" => $category->slug ]);
    }

    public function updated(Category $category) {
        if($category->slug !== $category->getOriginal("slug")) {
            $oldSlug = $category->getOriginal("slug");
            $searchSlugs = SearchSlug::where("category_part", $oldSlug)->get();
            $searchSlugs->each(function ($searchSlug) use ($category, $oldSlug) {
                $searchSlug->category_part = $category->slug;
                $searchSlug->save();
            });
            $category->descendants->each(function($descendant) use ($category, $oldSlug) {
                $descendant->slug = Str::replaceFirst($category->slug, $oldSlug, $descendant->slug);
                $descendant->save();
            });
        }
    }
}
