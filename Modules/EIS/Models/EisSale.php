<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisSale extends Model
{
    protected $table = 'eis_sales';

    protected $fillable = [
        'business_id',
        'transaction_id',
        'invoice_number',
        'fiscal_invoice_number',
        'receipt_number',
        'receipt_signature',
        'qr_code',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
        'submitted_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }
}