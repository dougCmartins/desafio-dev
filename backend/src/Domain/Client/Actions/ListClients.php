<?php

declare(strict_types=1);

namespace Domain\Client\Actions;

use Domain\Client\Data\ClientData;
use Domain\Client\Models\Client;
use Illuminate\Support\Facades\Log;

final class ListClients
{
    /**
     * @return array<int, ClientData>
     */
    public function handle(): array
    {
        $clients = Client::query()
            ->with(['users', 'stores'])
            ->get()
            ->map(fn (Client $client): ClientData => $this->toData($client))
            ->all();

        Log::info('Clients listed', [
            'count' => count($clients),
        ]);

        return $clients;
    }

    private function toData(Client $client): ClientData
    {
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
