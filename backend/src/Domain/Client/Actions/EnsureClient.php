<?php

declare(strict_types=1);

namespace Domain\Client\Actions;

use Domain\Client\Data\EnsureClientData;
use Domain\Client\Data\EnsuredClientData;
use Domain\Client\Models\Client;
use Domain\Client\Models\Store;
use Domain\Client\Models\User;
use Illuminate\Support\Facades\Log;

final class EnsureClient
{
    public function handle(EnsureClientData $data): EnsuredClientData
    {
        $client = Client::query()->where('cpf', $data->cpf)->first();

        if ($client === null) {
            $user = User::query()->create([
                'name' => $data->name,
            ]);

            $client = Client::query()->create([
                'cpf' => $data->cpf,
                'card' => $data->card,
                'user_id' => $user->id,
                'amount' => 0,
            ]);

            $store = Store::query()->create([
                'name' => $data->store_name,
                'owner_id' => $client->id,
            ]);

            Log::info('Client ensured', [
                'client_id' => $client->id,
                'created' => true,
            ]);

            return new EnsuredClientData(
                client_id: (int) $client->id,
                store_id: (int) $store->id,
                amount: (float) $client->amount,
            );
        }

        $store = $client->stores;

        if ($store === null) {
            $store = Store::query()->create([
                'name' => $data->store_name,
                'owner_id' => $client->id,
            ]);
        }

        Log::info('Client ensured', [
            'client_id' => $client->id,
            'created' => false,
        ]);

        return new EnsuredClientData(
            client_id: (int) $client->id,
            store_id: (int) $store->id,
            amount: (float) $client->amount,
        );
    }
}
