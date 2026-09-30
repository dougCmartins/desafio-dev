<?php

declare(strict_types=1);

namespace Domain\Client\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class EnsureClientData extends Data
{
    public function __construct(
        #[Required, StringType, Max(11)]
        public readonly string $cpf,
        #[Required, StringType, Max(12)]
        public readonly string $card,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType, Max(80)]
        public readonly string $store_name,
    ) {
    }
}
