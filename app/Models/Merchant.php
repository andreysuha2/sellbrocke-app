<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Merchant extends Authenticatable
{
    use SoftDeletes, HasApiTokens;

    protected $guard = "merchant";

    protected $fillable = [ "name", "login", "password" ];

    protected $hidden = [ "password" ];

    public function findForPassport($merchantLogin) {
        return $this->where('login', $merchantLogin)->first();
    }

    public function customers() {
        return $this->hasMany("App\Models\Customer", "merchant_id", "id");
    }

    public function getCustomerByMerchantId($id) {
        return $this->customers()->where("merchant_customer_id", $id);
    }
}
