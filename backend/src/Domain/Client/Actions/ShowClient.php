<?php

declare(strict_types=1);

namespace Domain\Client\Actions;

use Domain\Client\Data\ClientData;
use Domain\Client\Data\ShowClientData;
use Domain\Client\Exceptions\ClientNotFoundException;
use Domain\Client\Models\Client;
use Illuminate\Support\Facades\Log;

final class ShowClient
{
    public function handle(ShowClientData $data): ClientData
    {
        $client = Client::query()->with(['users', 'stores'])->find($data->id);

        if ($client === null) {
            Log::warning('Client not found', [
                'client_id' => $data->id,
            ]);

            throw new ClientNotFoundException();
        }

        Log::info('Client found', [
            'client_id' => $client->id,
        ]);

        $store = $client->stores;

        return new ClientData(
            id: (int) $client->id,
            cpf: (string) $client->cpf,
            card: (string) $client->card,
            amount: (float) $client->amount,
            name: (string) $client->users->name,
            store_name: $store === null ? null : (string) $store->name,
        );
    }
}
