<?php

declare(strict_types=1);

namespace Domain\Transaction\Tests\Feature;

use Domain\Client\Models\Store;
use Domain\Client\Models\User;
use Domain\Client\Models\Client;
use Domain\Transaction\Models\Operation;
use Domain\Transaction\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItListsTransactionsInsideTheResponseEnvelope(): void
    {
        $response = $this->getJson('/api/transactions');

        $response->assertOk()
            ->assertJson([
                'data' => [],
                'message' => 'Transactions listed successfully.',
                'code' => 'TRANSACTIONS_LISTED',
                'status_code' => 200,
                'errors' => [],
            ]);
    }

    public function testItShowsAnExistingTransaction(): void
    {
        $operation = $this->createOperation(1, 'Debit', 'Inflow', 1);
        $user = User::query()->create([
            'name' => 'Ada',
        ]);
        $client = Client::query()->create([
            'cpf' => '09620676017',
            'card' => '4753****3153',
            'user_id' => $user->id,
            'amount' => 10,
        ]);
        $store = Store::query()->create([
            'name' => 'Acme',
            'owner_id' => $client->id,
        ]);
        $transaction = Transaction::query()->create([
            'client_id' => $client->id,
            'store_id' => $store->id,
            'type' => $operation->code_operation,
            'value' => 10,
            'amount' => 10,
            'date_at' => '2022-09-01',
            'hour_at' => '15:30:00',
        ]);

        $response = $this->getJson('/api/transactions/'.$transaction->id);

        $response->assertOk()
            ->assertJsonPath('code', 'TRANSACTION_FOUND')
            ->assertJsonPath('data.id', $transaction->id)
            ->assertJsonPath('data.description', 'Debit')
            ->assertJsonPath('data.type_description', 'Inflow');
    }

    public function testItReturnsNotFoundWhenTheTransactionDoesNotExist(): void
    {
        $response = $this->getJson('/api/transactions/999');

        $response->assertNotFound()
            ->assertJson([
                'data' => null,
                'message' => 'Transaction not found.',
                'code' => 'TRANSACTION_NOT_FOUND',
                'status_code' => 404,
                'errors' => [],
            ]);
    }

    public function testItListsOperationsInsideTheResponseEnvelope(): void
    {
        $this->createOperation(1, 'Debit', 'Inflow', 1);

        $response = $this->getJson('/api/operations');

        $response->assertOk()
            ->assertJsonPath('code', 'OPERATIONS_LISTED')
            ->assertJsonPath('data.0.description', 'Debit')
            ->assertJsonPath('data.0.type_description', 'Inflow');
    }

    public function testItReturnsNotFoundWhenTheOperationDoesNotExist(): void
    {
        $response = $this->getJson('/api/operations/999');

        $response->assertNotFound()
            ->assertJson([
                'data' => null,
                'message' => 'Operation not found.',
                'code' => 'OPERATION_NOT_FOUND',
                'status_code' => 404,
                'errors' => [],
            ]);
    }

    private function createOperation(int $code, string $description, string $typeDescription, int $type): Operation
    {
        return Operation::query()->create([
            'code_operation' => $code,
            'description' => $description,
            'type_description' => $typeDescription,
            'type' => $type,
        ]);
    }
}
