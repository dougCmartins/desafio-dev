<?php

declare(strict_types=1);

namespace Domain\Transaction\Controllers;

use Domain\Transaction\Actions\ListTransactions;
use Domain\Transaction\Actions\ShowTransaction;
use Domain\Transaction\Data\ShowTransactionData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Domain\Orchestrator\Import\Actions\ImportTransaction;
use Domain\Orchestrator\Import\Data\ImportTransactionData;

final class TransactionController
{
    public function index(ListTransactions $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(),
            'message' => 'Transactions listed successfully.',
            'code' => 'TRANSACTIONS_LISTED',
            'status_code' => 200,
            'errors' => [],
        ]);
    }

    public function show(string $id, ShowTransaction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(ShowTransactionData::validate(['id' => $id])),
            'message' => 'Transaction found successfully.',
            'code' => 'TRANSACTION_FOUND',
            'status_code' => 200,
            'errors' => [],
        ]);
    }

    public function store(Request $request, ImportTransaction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(ImportTransactionData::validate($request->all())),
            'message' => 'Transaction recorded successfully.',
            'code' => 'TRANSACTION_RECORDED',
            'status_code' => 200,
            'errors' => [],
        ]);
    }
}
