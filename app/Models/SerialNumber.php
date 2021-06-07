<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $table = "serial_numbers";
    public $timestamps = false;

    public function salesNumbers()
    {
        return $this->hasMany('App\Models\SalesNumber', 'serial_number_id', 'id');
    }
}
