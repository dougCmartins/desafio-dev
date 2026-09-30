<?php

declare(strict_types=1);

namespace Domain\Transaction\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class ResolveOperationData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(0)]
        public readonly int $code_operation,
    ) {
    }
}
