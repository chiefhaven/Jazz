<?php

namespace App\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisTerminal extends Model
{
    protected $fillable = [
        'terminal_id',
        'activation_code',
        'status',
        'device_fingerprint',
        'product_id',
        'product_version',
        'os',
        'mac_address',
        'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];
}