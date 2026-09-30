<?php

declare(strict_types=1);

namespace Domain\Client\Tests\Feature;

use App\Models\Store\Store;
use App\Models\User;
use Domain\Client\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItListsClientsInsideTheResponseEnvelope(): void
    {
        $response = $this->getJson('/api/clients');

        $response->assertOk()
            ->assertJson([
                'data' => [],
                'message' => 'Clients listed successfully.',
                'code' => 'CLIENTS_LISTED',
                'status_code' => 200,
                'errors' => [],
            ]);
    }

    public function testItShowsAnExistingClient(): void
    {
        $client = $this->createClient();

        $response = $this->getJson('/api/clients/' . $client->id);

        $response->assertOk()
            ->assertJsonPath('code', 'CLIENT_FOUND')
            ->assertJsonPath('data.id', $client->id)
            ->assertJsonPath('data.cpf', '09620676017')
            ->assertJsonPath('data.name', 'Ada')
            ->assertJsonPath('data.store_name', 'Acme');

        $payload = $response->json('data');
        $this->assertIsArray($payload);
        $this->assertArrayNotHasKey('users', $payload);
        $this->assertArrayNotHasKey('stores', $payload);
        $this->assertArrayNotHasKey('transactions', $payload);
    }

    public function testItReturnsNotFoundWhenTheClientDoesNotExist(): void
    {
        $response = $this->getJson('/api/clients/999');

        $response->assertNotFound()
            ->assertJson([
                'data' => null,
                'message' => 'Client not found.',
                'code' => 'CLIENT_NOT_FOUND',
                'status_code' => 404,
                'errors' => [],
            ]);
    }

    private function createClient(): Client
    {
        $user = User::query()->create([
            'name' => 'Ada',
        ]);

        $client = Client::query()->create([
            'cpf' => '09620676017',
            'card' => '4753****3153',
            'user_id' => $user->id,
            'amount' => 10.5,
        ]);

        Store::query()->create([
            'name' => 'Acme',
            'owner_id' => $client->id,
        ]);

        return $client;
    }
}
