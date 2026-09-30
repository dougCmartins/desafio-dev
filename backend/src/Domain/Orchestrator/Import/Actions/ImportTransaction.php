<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Import\Actions;

use Domain\Client\Actions\EnsureClient;
use Domain\Client\Actions\UpdateClientAmount;
use Domain\Client\Data\EnsureClientData;
use Domain\Client\Data\UpdateClientAmountData;
use Domain\Transaction\Actions\RecordTransaction;
use Domain\Transaction\Actions\ResolveOperation;
use Domain\Transaction\Data\RecordTransactionData;
use Domain\Transaction\Data\ResolveOperationData;
use Domain\Transaction\Data\TransactionData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Domain\Orchestrator\Import\Data\ImportTransactionData;

final class ImportTransaction
{
    public function __construct(
        private readonly EnsureClient $ensureClient,
        private readonly ResolveOperation $resolveOperation,
        private readonly RecordTransaction $recordTransaction,
        private readonly UpdateClientAmount $updateClientAmount,
    ) {
    }

    public function handle(ImportTransactionData $data): TransactionData
    {
        $transaction = DB::transaction(function () use ($data): TransactionData {
            $client = $this->ensureClient->handle(new EnsureClientData(
                cpf: $data->cpf,
                card: $data->card,
                name: $data->name,
                store_name: $data->store_name,
            ));

            $operation = $this->resolveOperation->handle(new ResolveOperationData(
                code_operation: $data->type,
            ));

            $amount = $operation->appliesOutflow()
                ? $client->amount - $data->value
                : $client->amount + $data->value;

            $recorded = $this->recordTransaction->handle(new RecordTransactionData(
                client_id: $client->client_id,
                store_id: $client->store_id,
                type: $data->type,
                value: $data->value,
                amount: $amount,
                date_at: $data->date_at,
                hour_at: $data->hour_at,
            ));

            $this->updateClientAmount->handle(new UpdateClientAmountData(
                client_id: $client->client_id,
                amount: $amount,
            ));

            return $recorded;
        });

        Log::info('Transaction imported', [
            'transaction_id' => $transaction->id,
            'client_id' => $transaction->client_id,
        ]);

        return $transaction;
    }
}
