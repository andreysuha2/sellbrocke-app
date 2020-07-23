<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    // TODO: make softdelete
    protected $table = "defects";

    protected $fillable = [ "name", "description", "price_reduction" ];

    protected $hidden = [ "pivot" ];

    public function categories() {
        return $this->belongsToMany("App\Models\Category", "category_defect", "defect_id", "category_id");
    }

    static function getList($query = false) {
        $q = self::select("id", "name");
        return $query ? $q : $q->get();
    }
}
