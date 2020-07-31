<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchSlug extends Model
{
    protected $table = "search_slugs";

    protected $fillable = [ "category_part", "company_part", "device_part" ];

    public $timestamps = false;

    public function search() {
        return $this->morphTo();
    }
}
