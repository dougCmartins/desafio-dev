<?php

declare(strict_types=1);

namespace Domain\Transaction\Controllers;

use Domain\Transaction\Actions\ListOperations;
use Domain\Transaction\Actions\ShowOperation;
use Domain\Transaction\Data\ShowOperationData;
use Illuminate\Http\JsonResponse;

final class OperationController
{
    public function index(ListOperations $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(),
            'message' => 'Operations listed successfully.',
            'code' => 'OPERATIONS_LISTED',
            'status_code' => 200,
            'errors' => [],
        ]);
    }

    public function show(string $id, ShowOperation $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(ShowOperationData::validate(['id' => $id])),
            'message' => 'Operation found successfully.',
            'code' => 'OPERATION_FOUND',
            'status_code' => 200,
            'errors' => [],
        ]);
    }
}
