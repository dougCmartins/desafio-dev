<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Data\ShowTransactionData;
use Domain\Transaction\Data\TransactionData;
use Domain\Transaction\Exceptions\TransactionNotFoundException;
use Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Log;

final class ShowTransaction
{
    public function handle(ShowTransactionData $data): TransactionData
    {
        $transaction = Transaction::query()->with('operations')->find($data->id);

        if ($transaction === null) {
            Log::warning('Transaction not found', [
                'transaction_id' => $data->id,
            ]);

            throw new TransactionNotFoundException();
        }

        Log::info('Transaction found', [
            'transaction_id' => $transaction->id,
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
