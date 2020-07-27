<?php

namespace App\Models;

use Bnb\Laravel\Attachments\HasAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use SoftDeletes, HasAttachment, NodeTrait;

    protected $table = "categories";

    protected $fillable = [ "name", "slug", "description" ];

    protected $hidden = [ "pivot" ];

    public function defects() {
        return $this->belongsToMany("App\Models\Defect", "category_defect", "category_id", "defect_id");
    }

    public function devices() {
        return $this->belongsToMany("App\Models\Device", "category_device", "category_id", "device_id");
    }

    public function childrenDevices() {
        $categories = $this->descendants()->pluck("id");
        return Device::whereHas("categories", function ($query) use ($categories) {
            $query->whereIn("category_id", $categories);
        });
    }

    public function setParentAttribute($value) {
        $this->setParentIdAttribute($value);
    }
}
