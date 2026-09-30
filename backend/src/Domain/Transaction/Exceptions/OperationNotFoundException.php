<?php

declare(strict_types=1);

namespace Domain\Transaction\Exceptions;

use Domain\Shared\Exceptions\DomainException;

final class OperationNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Operation not found.');
    }

    public function getErrorCode(): string
    {
        return 'OPERATION_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 422;
    }
}
