<?php

namespace App\Models;

use Bnb\Laravel\Attachments\HasAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes, HasAttachment;

    protected $table = "devices";

    protected $fillable = [ "name", "description", "base_price", "slug", "use_products_grids" ];

    protected $hidden = [ "company_id" ];

    public function company() {
        return $this->belongsTo("App\Models\Company", "company_id");
    }

    public function categories() {
        return $this->belongsToMany("App\Models\Category", "category_device", "device_id", "category_id");
    }

    public function productsGrids() {
        return $this->belongsToMany("App\Models\ProductGrid", "device_product_grid", "device_id", "product_grid_id");
    }

    public function searchSlugs() {
        return $this->morphMany("App\Models\SearchSlug", "search");
    }

    // defects relation by example from https://stackoverflow.com/questions/37430217/has-many-through-many-to-many
    public function defects() {
        return Defect::join("category_defect", "defects.id", "=", "category_defect.defect_id")
                ->join("categories", "category_defect.category_id", "=", "categories.id")
                ->join("category_device", "categories.id", "=", "category_device.id")
                ->join("devices", "category_device.device_id", "=", "devices.id")
                ->where("devices.id", $this->id);
    }

    public function getDefectsAttribute() {
        if(!$this->relationLoaded('categories') || !$this->categories->first()->relationLoaded("defects")) {
            $this->load('categories.defects');
        }

        return collect($this->categories->lists("defects"))->collapse()->unique();
    }
}
