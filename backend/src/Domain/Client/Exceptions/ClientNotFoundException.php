<?php

declare(strict_types=1);

namespace Domain\Client\Exceptions;

use Domain\Shared\Exceptions\DomainException;

final class ClientNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Client not found.');
    }

    public function getErrorCode(): string
    {
        return 'CLIENT_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 404;
    }
}
