<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Data\ResolveOperationData;
use Domain\Transaction\Data\ResolvedOperationData;
use Domain\Transaction\Exceptions\OperationNotFoundException;
use Domain\Transaction\Models\Operation;
use Illuminate\Support\Facades\Log;

final class ResolveOperation
{
    public function handle(ResolveOperationData $data): ResolvedOperationData
    {
        $operation = Operation::query()
            ->where('code_operation', $data->code_operation)
            ->first();

        if ($operation === null) {
            Log::warning('Operation not found', [
                'code_operation' => $data->code_operation,
            ]);

            throw new OperationNotFoundException();
        }

        Log::info('Operation resolved', [
            'code_operation' => $operation->code_operation,
            'type' => $operation->type,
        ]);

        return new ResolvedOperationData(
            code_operation: (int) $operation->code_operation,
            type: (int) $operation->type,
            description: (string) $operation->description,
            type_description: (string) $operation->type_description,
        );
    }
}
