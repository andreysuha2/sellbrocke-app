<?php

namespace App\Models;

use Bnb\Laravel\Attachments\HasAttachment;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasAttachment;

    protected $table = "companies";

    protected $fillable = [ "name", "price_reduction", "slug" ];

    public function devices() {
        return $this->hasMany("App\Models\Device", "company_id", "id");
    }

    public function searchSlugs() {
        return $this->morphMany("App\Models\SearchSlug", "search");
    }
}
