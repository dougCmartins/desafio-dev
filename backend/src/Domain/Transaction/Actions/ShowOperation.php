<?php

declare(strict_types=1);

namespace Domain\Transaction\Actions;

use Domain\Transaction\Data\OperationData;
use Domain\Transaction\Data\ShowOperationData;
use Domain\Transaction\Exceptions\MissingOperationException;
use Domain\Transaction\Models\Operation;
use Illuminate\Support\Facades\Log;

final class ShowOperation
{
    public function handle(ShowOperationData $data): OperationData
    {
        $operation = Operation::query()->find($data->id);

        if ($operation === null) {
            Log::warning('Operation not found', [
                'operation_id' => $data->id,
            ]);

            throw new MissingOperationException();
        }

        Log::info('Operation found', [
            'operation_id' => $operation->id,
        ]);

        return new OperationData(
            id: (int) $operation->id,
            code_operation: (int) $operation->code_operation,
            description: (string) $operation->description,
            type_description: (string) $operation->type_description,
            type: (int) $operation->type,
        );
    }
}
