<?php

namespace App\Observers;
use App\Models\Category;
use App\Models\Device;
use App\Models\SearchSlug;

class DeviceObserver
{
    public function forceDeleted(Device $device) {
        $device->categories()->detach();
        $device->productsGrids()->detach();
        $device->searchSlugs()->delete();
    }

    public function pivotAttached(Device $device, $relationName) {
        if($relationName === "categories") {
            $searchSlugsDevice = $device->categories->filter(function ($category) use ($device) {
                return !SearchSlug::where("slug", $category->slug . "/" . $device->company->slug . "/" . $device->slug)->exists();
            })->map(function ($category) use ($device) {
                return [
                    "category_part" => $category->slug,
                    "company_part" => $device->company->slug,
                    "device_part" => $device->slug
                ];
            });
            $searchSlugsCompany = $device->categories->filter(function ($category) use ($device) {
                return !SearchSlug::where("slug", $category->slug . "/" . $device->company->slug)->exists();
            })->map(function ($category) use ($device) {
                return [
                    "category_part" => $category->slug,
                    "company_part" => $device->company->slug
                ];
            });
            $device->company->searchSlugs()->createMany($searchSlugsCompany);
            $device->searchSlugs()->createMany($searchSlugsDevice->toArray());
        }
    }

    public function pivotDetached(Device $device, $relationName, $ids) {
        if($relationName === "categories") {
            $categories = Category::select("slug")->whereIn("id", $ids)->get();
            $query = $device->searchSlugs();
            $categories->each(function ($category) use (&$query) {
                $query = $query->where("slug", "like", "$category->slug%");
            });
            $query->delete();
        }
    }

    public function updating(Device $device) {
        if($device->slug !== $device->getOriginal("slug")) {
            $device->searchSlugs->each(function ($searchSlug) use ($device) {
                $searchSlug->device_part = $device->slug;
                $searchSlug->save();
            });
        }
    }
}
