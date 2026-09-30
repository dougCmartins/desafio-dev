<?php

declare(strict_types=1);

namespace Domain\Transaction\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class RecordTransactionData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $client_id,
        #[Required, IntegerType, Min(1)]
        public readonly int $store_id,
        #[Required, IntegerType, Min(0)]
        public readonly int $type,
        #[Required, Numeric]
        public readonly float $value,
        #[Required, Numeric]
        public readonly float $amount,
        #[Required, StringType]
        public readonly string $date_at,
        #[Required, StringType]
        public readonly string $hour_at,
    ) {
    }
}
