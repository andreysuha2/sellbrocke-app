<?php

namespace App\Models;

use Bnb\Laravel\Attachments\HasAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes, HasAttachment;

    protected $table = "devices";

    protected $fillable = [ "name", "description", "base_price", "slug" ];

    protected $hidden = [ "company_id" ];

    public function company() {
        return $this->belongsTo("App\Models\Company", "company_id");
    }

    public function categories() {
        return $this->belongsToMany("App\Models\Device", "category_device", "device_id", "category_id");
    }
}
