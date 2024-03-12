<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    public function __construct(string $message = "Bad request", int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
