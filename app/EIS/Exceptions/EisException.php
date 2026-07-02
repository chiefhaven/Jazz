<?php

namespace App\EIS\Exceptions;

use Exception;

class EisException extends Exception
{
    protected array $context = [];

    public function __construct(string $message = "", int $code = 0, array $context = [])
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}