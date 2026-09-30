<?php

declare(strict_types=1);

namespace Domain\Transaction\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class ResolvedOperationData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(0)]
        public readonly int $code_operation,
        #[Required, IntegerType]
        public readonly int $type,
        #[Required, StringType, Max(80)]
        public readonly string $description,
        #[Required, StringType, Max(80)]
        public readonly string $type_description,
    ) {
    }

    public function appliesOutflow(): bool
    {
        return $this->type === 0;
    }
}
