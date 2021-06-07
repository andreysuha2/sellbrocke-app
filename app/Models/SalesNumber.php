<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesNumber extends Model
{
//    protected $table = "sales_numbers";
    public $timestamps = false;

    public function serialNumber()
    {
        return $this->belongsTo('App\Models\SerialNumber');
    }
}
