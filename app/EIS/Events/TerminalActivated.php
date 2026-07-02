<?php

namespace App\EIS\Events;

class TerminalActivated
{
    public function __construct(
        public string $terminalId
    ) {}
}