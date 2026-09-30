<?php

declare(strict_types=1);

namespace Domain\Client\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpdateClientAmountData extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public readonly int $client_id,
        #[Required, Numeric]
        public readonly float $amount,
    ) {
    }
}
