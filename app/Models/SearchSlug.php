<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchSlug extends Model
{
    protected $table = "search_slug";

    protected $fillable = [ "slug" ];

    public $timestamps = false;

    public function search() {
        return $this->morphTo();
    }
}
