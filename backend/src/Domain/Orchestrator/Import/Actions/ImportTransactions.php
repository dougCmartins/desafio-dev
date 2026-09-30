<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Import\Actions;

use Domain\Client\Actions\ListClients;
use Domain\Orchestrator\Import\Data\ImportedFileData;
use Domain\Orchestrator\Import\Data\ImportTransactionData;
use Domain\Transaction\Actions\ListTransactions;
use Illuminate\Support\Facades\DB;

final class ImportTransactions
{
    public function __construct(
        private readonly ImportTransaction $importTransaction,
        private readonly ListClients $listClients,
        private readonly ListTransactions $listTransactions,
    ) {
    }

    /**
     * @param array<int, ImportTransactionData> $rows
     */
    public function handle(array $rows): ImportedFileData
    {
        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                $this->importTransaction->handle($row);
            }
        });

        return new ImportedFileData(
            clients: $this->listClients->handle(),
            transactions: $this->listTransactions->handle(),
        );
    }
}
