<?php

declare(strict_types=1);

namespace Domain\Shared\Exceptions;

use DomainException as BaseDomainException;

abstract class DomainException extends BaseDomainException
{
    abstract public function getErrorCode(): string;

    abstract public function getHttpStatus(): int;
}
