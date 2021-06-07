<?php

namespace App\Models;

use Bnb\Laravel\Attachments\HasAttachment;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes, HasAttachment, PivotEventTrait;

    protected $table = "devices";

    protected $fillable = [ "name", "description", "base_price", "slug", "use_products_grids" ];

    protected $hidden = [ "company_id" ];

    public function company() {
        return $this->belongsTo('App\Models\Company', "company_id");
    }

    public function categories() {
        return $this->belongsToMany('App\Models\Category', "category_device", "device_id", "category_id");
    }

    public function productsGrids() {
        return $this->belongsToMany('App\Models\ProductGrid', "device_product_grid", "device_id", "product_grid_id");
    }

    public function searchSlugs() {
        return $this->morphMany('App\Models\SearchSlug', "search");
    }

    public function defects() {
        $categories = $this->categories->map(function ($category) {
            return Category::ancestorsAndSelf($category->id);
        });
        $categoriesIds = $categories->flatten()->unique("id")->pluck("id");
        return Defect::whereHas("categories", function ($query) use ($categoriesIds) {
            $query->whereIn("categories.id", $categoriesIds);
        });
    }
}
