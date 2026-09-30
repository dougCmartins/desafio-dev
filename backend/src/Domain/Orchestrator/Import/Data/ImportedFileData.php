<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Import\Data;

use Domain\Client\Data\ClientData;
use Domain\Transaction\Data\TransactionData;

final class ImportedFileData
{
    /**
     * @param array<int, ClientData> $clients
     * @param array<int, TransactionData> $transactions
     */
    public function __construct(
        public readonly array $clients,
        public readonly array $transactions,
    ) {
    }
}
