<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingFeature extends Model
{
    protected $fillable = ['pricing_item_id', 'feature'];

    public function pricingItem()
    {
        return $this->belongsTo(PricingItem::class);
    }
}