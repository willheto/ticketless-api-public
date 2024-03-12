<?php

namespace App\Exceptions\CustomValidationException;

use Exception;

class CustomValidationException extends Exception
{
    public function __construct(string $message = "Unprocessable entity", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
