<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    protected $table = "order_log";

    protected $fillable = [ "message" ];

    public function order() {
        return $this->belongsTo("App\Models\Order", "order_id");
    }
}
