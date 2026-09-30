<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Data\RecordTransactionData;
use Domain\Transaction\Data\TransactionData;
use Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Log;

final class RecordTransaction
{
    public function handle(RecordTransactionData $data): TransactionData
    {
        $transaction = new Transaction();
        $transaction->fill([
            'client_id' => $data->client_id,
            'store_id' => $data->store_id,
            'type' => $data->type,
            'value' => $data->value,
            'amount' => $data->amount,
            'date_at' => $data->date_at,
            'hour_at' => $data->hour_at,
        ]);
        $transaction->save();
        $transaction->load('operations');

        Log::info('Transaction recorded', [
            'transaction_id' => $transaction->id,
            'client_id' => $transaction->client_id,
        ]);

        $operation = $transaction->operations;

        return new TransactionData(
            id: (int) $transaction->id,
            client_id: (int) $transaction->client_id,
            value: (float) $transaction->value,
            amount: (float) $transaction->amount,
            date_at: (string) $transaction->date_at,
            hour_at: (string) $transaction->hour_at,
            description: $operation === null ? '' : (string) $operation->description,
            type_description: $operation === null ? '' : (string) $operation->type_description,
        );
    }
}
