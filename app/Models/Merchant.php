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
}
