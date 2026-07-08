<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisTaxRate extends Model
{
    protected $fillable = [

        'configuration_id',
        'eis_tax_rate_id',
        'name',
        'charge_mode',
        'ordinal',
        'rate',

    ];


    public function configuration()
    {
        return $this->belongsTo(
            EisConfiguration::class,
            'configuration_id'
        );
    }
}