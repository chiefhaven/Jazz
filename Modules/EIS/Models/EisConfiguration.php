<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisConfiguration extends Model
{
    protected $fillable = [
        'business_id',
        'global_version',
        'terminal_version',
        'taxpayer_version',
        'tin',
        'is_vat_registered',
        'tax_office_code',
        'raw_response',
        'last_synced_at',
    ];


    protected $casts = [
        'raw_response' => 'array',
        'is_vat_registered' => 'boolean',
        'last_synced_at' => 'datetime',
    ];


    public function taxRates()
    {
        return $this->hasMany(
            EisTaxRate::class,
            'configuration_id'
        );
    }


    public function terminal()
    {
        return $this->hasOne(
            EisTerminalConfiguration::class,
            'configuration_id'
        );
    }


    public function levies()
    {
        return $this->hasMany(
            EisLevy::class,
            'configuration_id'
        );
    }
}