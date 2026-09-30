<?php

declare(strict_types=1);

namespace Domain\Transaction\Exceptions;

use Domain\Shared\Exceptions\DomainException;

final class TransactionNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Transaction not found.');
    }

    public function getErrorCode(): string
    {
        return 'TRANSACTION_NOT_FOUND';
    }

    public function getHttpStatus(): int
    {
        return 404;
    }
}
