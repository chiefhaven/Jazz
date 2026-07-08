<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisTerminalConfiguration extends Model
{

    protected $fillable = [

        'configuration_id',
        'version_no',
        'terminal_label',
        'is_active_terminal',
        'email_address',
        'phone_number',
        'trading_name',
        'site_id',
        'site_name',
        'max_transaction_age_hours',
        'max_cumulative_amount',
        'address_lines',

    ];


    protected $casts = [

        'address_lines'=>'array',

        'is_active_terminal'=>'boolean',

    ];


    public function configuration()
    {
        return $this->belongsTo(
            EisConfiguration::class,
            'configuration_id'
        );
    }

}