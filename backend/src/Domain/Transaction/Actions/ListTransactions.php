<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Data\TransactionData;
use Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Log;

final class ListTransactions
{
    /**
     * @return array<int, TransactionData>
     */
    public function handle(): array
    {
        $transactions = Transaction::query()
            ->with('operations')
            ->get()
            ->map(fn (Transaction $transaction): TransactionData => $this->toData($transaction))
            ->all();

        Log::info('Transactions listed', [
            'count' => count($transactions),
        ]);

        return $transactions;
    }

    private function toData(Transaction $transaction): TransactionData
    {
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
