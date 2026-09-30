<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Import\Actions;

use Domain\Client\Actions\ClearClients;
use Domain\Transaction\Actions\ClearTransactions;
use Illuminate\Support\Facades\DB;

final class ClearImportedData
{
    public function __construct(
        private readonly ClearTransactions $clearTransactions,
        private readonly ClearClients $clearClients,
    ) {
    }

    public function handle(): void
    {
        DB::transaction(function (): void {
            $this->clearTransactions->handle();
            $this->clearClients->handle();
        });
    }
}
