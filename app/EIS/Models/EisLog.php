<?php

namespace App\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisLog extends Model
{
    protected $fillable = [
        'type',
        'action',
        'reference',
        'request_payload',
        'response_payload',
        'status',
        'message',
    ];
}