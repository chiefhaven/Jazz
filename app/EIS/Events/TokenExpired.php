<?php

namespace App\EIS\Events;

class TokenExpired
{
    public function __construct(
        public string $reason = 'Token expired or invalid'
    ) {}
}