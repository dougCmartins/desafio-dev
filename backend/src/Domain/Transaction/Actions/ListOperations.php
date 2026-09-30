<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Data\OperationData;
use Domain\Transaction\Models\Operation;
use Illuminate\Support\Facades\Log;

final class ListOperations
{
    /**
     * @return array<int, OperationData>
     */
    public function handle(): array
    {
        $operations = Operation::query()
            ->get()
            ->map(fn (Operation $operation): OperationData => new OperationData(
                id: (int) $operation->id,
                code_operation: (int) $operation->code_operation,
                description: (string) $operation->description,
                type_description: (string) $operation->type_description,
                type: (int) $operation->type,
            ))
            ->all();

        Log::info('Operations listed', [
            'count' => count($operations),
        ]);

        return $operations;
    }
}
