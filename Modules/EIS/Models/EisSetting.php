<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisSetting extends Model
{
    protected $fillable = [
        'business_id',
        'base_url',
        'jwt_token',
        'refresh_token',
        'token_expires_at',
        'secret_key',
        'tpin',
        'device_id',
        'branch_id',
        'status',
    ];
}