<?php

namespace App\Http\Resources\Merchants;

use Illuminate\Http\Resources\Json\JsonResource;

class Merchant extends JsonResource
{

    public static $wrap = "merchant";
    private $forPlugin;

    public function __construct($resource, $forPlugin = true)
    {
        parent::__construct($resource);
        $this->forPlugin = $forPlugin;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "login" => $this->when(!$this->forPlugin, $this->login),
            "url" => $this->when(!$this->forPlugin, $this->url),
            "createdAt" => $this->when(!$this->forPlugin, $this->created_at),
            "updatedAt" => $this->when(!$this->forPlugin, $this->updated_at),
            "deletedAt" => $this->when(!$this->forPlugin, $this->deleted_at)
        ];
    }
}
