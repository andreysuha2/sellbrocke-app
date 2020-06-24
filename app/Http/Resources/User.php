<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class User extends JsonResource
{
    public function __construct($resource, $hideEmail = false)
    {
        parent::__construct($resource);
        $this->hideEmail = $hideEmail;
    }

    private $hideEmail;
    public static $wrap = "user";
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hideEmail = $this->hideEmail || Auth::user()->id !== $this->id;
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->when(!$hideEmail, $this->email),
            "verified" => $this->when(!$hideEmail, (boolean) $this->email_verified_at)
        ];
    }
}
