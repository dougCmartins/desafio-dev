<?php

declare(strict_types=1);

namespace Domain\Transaction\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class TransactionData extends Data
{
    public function __construct(
        #[Required, Min(1)]
        public readonly int $id,
        #[Required, Min(1)]
        public readonly int $client_id,
        #[Required, Numeric]
        public readonly float $value,
        #[Required, Numeric]
        public readonly float $amount,
        #[Required, StringType]
        public readonly string $date_at,
        #[Required, StringType]
        public readonly string $hour_at,
        #[Required, StringType, Max(80)]
        public readonly string $description,
        #[Required, StringType, Max(80)]
        public readonly string $type_description,
    ) {
    }
}
