<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Import\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class ImportTransactionData extends Data
{
    public function __construct(
        #[Required, StringType, Max(11)]
        public readonly string $cpf,
        #[Required, StringType, Max(12)]
        public readonly string $card,
        #[Required, StringType]
        public readonly string $date_at,
        #[Required, StringType]
        public readonly string $hour_at,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType, Max(80)]
        public readonly string $store_name,
        #[Required, IntegerType, Min(0)]
        public readonly int $type,
        #[Required, Numeric]
        public readonly float $value,
    ) {
    }
}
