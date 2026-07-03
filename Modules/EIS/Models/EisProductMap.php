<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisProductMap extends Model
{
    protected $fillable = [
        'business_id',
        'product_id',
        'eis_product_id',
        'sku',
        'last_synced_at'
    ];
}