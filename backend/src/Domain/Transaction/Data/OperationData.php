<?php

declare(strict_types=1);

namespace Domain\Transaction\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class OperationData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $id,
        #[Required, IntegerType, Min(0)]
        public readonly int $code_operation,
        #[Required, StringType, Max(80)]
        public readonly string $description,
        #[Required, StringType, Max(80)]
        public readonly string $type_description,
        #[Required, IntegerType]
        public readonly int $type,
    ) {
    }
}
