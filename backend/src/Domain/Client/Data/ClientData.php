<?php

declare(strict_types=1);

namespace Domain\Client\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class ClientData extends Data
{
    public function __construct(
        #[Required, Min(1)]
        public readonly int $id,
        #[Required, StringType, Max(11)]
        public readonly string $cpf,
        #[Required, StringType, Max(12)]
        public readonly string $card,
        #[Required, Numeric]
        public readonly float $amount,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType, Max(80)]
        public readonly ?string $store_name,
    ) {
    }
}
