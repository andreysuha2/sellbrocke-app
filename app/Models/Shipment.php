<?php

namespace App\Models;

use Bnb\Laravel\Attachments\HasAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Shipment extends Model
{
    use HasAttachment;

    protected $guarded = [ "order_id" ];

    public function order() {
        return $this->belongsTo("App\Models\Order");
    }

    public function storeLabel($label) {
        if($this->type === "UPS") {
            $imageData = str_replace(' ', '+', $label);
            $fileName = "label_" . time() . "_" . Str::random() . ".gif";
            Storage::disk('local')->put("public/{$fileName}", base64_decode($imageData));
            $this->attach("storage/{$fileName}", [ "key" => "label", "name" => "label" ]);
            Storage::disk('local')->delete("public/{$fileName}");
        }
    }
}
