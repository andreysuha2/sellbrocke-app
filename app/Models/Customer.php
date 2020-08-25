<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [ "merchant_customer_id", "email", "login", "first_name", "last_name", "data" ];

    public function merchant() {
        return $this->belongsTo("App\Models\Merchant", "merchant_id");
    }

    public function orders() {
        return $this->hasMany("App\Models\Order", "customer_id");
    }
}
