<?php

declare(strict_types=1);

namespace Domain\Client\Controllers;

use Domain\Client\Actions\ListClients;
use Domain\Client\Actions\ShowClient;
use Domain\Client\Data\ShowClientData;
use Illuminate\Http\JsonResponse;

final class ClientController
{
    public function index(ListClients $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(),
            'message' => 'Clients listed successfully.',
            'code' => 'CLIENTS_LISTED',
            'status_code' => 200,
            'errors' => [],
        ]);
    }

    public function show(string $id, ShowClient $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(ShowClientData::validate(['id' => $id])),
            'message' => 'Client found successfully.',
            'code' => 'CLIENT_FOUND',
            'status_code' => 200,
            'errors' => [],
        ]);
    }
}
