<?php

declare(strict_types=1);

namespace App\Identity\Application\Exception;

class UsernameAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Username already exists.');
    }
}