<?php

declare(strict_types=1);

namespace Domain\Client\Actions;

use Domain\Client\Data\UpdateClientAmountData;
use Domain\Client\Exceptions\ClientNotFoundException;
use Domain\Client\Models\Client;
use Illuminate\Support\Facades\Log;

final class UpdateClientAmount
{
    public function handle(UpdateClientAmountData $data): void
    {
        $client = Client::query()->find($data->client_id);

        if ($client === null) {
            Log::warning('Client not found', [
                'client_id' => $data->client_id,
            ]);

            throw new ClientNotFoundException();
        }

        $client->update([
            'amount' => $data->amount,
        ]);

        Log::info('Client amount updated', [
            'client_id' => $client->id,
            'amount' => $data->amount,
        ]);
    }
}
