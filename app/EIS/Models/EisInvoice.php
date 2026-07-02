<?php

namespace App\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'tin',
        'customer_name',
        'payload',
        'status',
        'attempts',
        'last_error',
        'submitted_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'submitted_at' => 'datetime',
    ];
}