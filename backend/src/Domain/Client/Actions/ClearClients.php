<?php

declare(strict_types=1);

namespace Domain\Client\Actions;

use Domain\Client\Models\Client;
use Domain\Client\Models\Store;
use Domain\Client\Models\User;

final class ClearClients
{
    public function handle(): void
    {
        Store::query()->delete();
        Client::query()->delete();
        User::query()->delete();
    }
}
