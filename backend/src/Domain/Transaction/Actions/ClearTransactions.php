<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Models\Transaction;

final class ClearTransactions
{
    public function handle(): void
    {
        Transaction::query()->delete();
    }
}
