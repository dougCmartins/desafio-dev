<?php

declare(strict_types=1);

namespace Domain\Orchestrator\Import\Tests\Feature;

use Domain\Client\Models\Store;
use Domain\Client\Models\User;
use Domain\Client\Models\Client;
use Domain\Transaction\Models\Operation;
use Domain\Transaction\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ImportTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesTheOwnerClientStoreAndMovement(): void
    {
        $this->createOperation(1, 'Debit', 'Inflow', 1);

        $response = $this->postJson('/api/transactions', $this->payload(1, 100));

        $response->assertOk()
            ->assertJsonPath('code', 'TRANSACTION_RECORDED')
            ->assertJsonPath('data.description', 'Debit')
            ->assertJsonPath('data.amount', 100);

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, Client::query()->count());
        $this->assertSame('Acme', Store::query()->first()->name);
        $this->assertSame(100.0, (float) Client::query()->first()->amount);
        $this->assertSame(1, Transaction::query()->count());
    }

    public function testItSubtractsTheBalanceWhenTheOperationIsAnOutflow(): void
    {
        $this->createOperation(1, 'Debit', 'Inflow', 1);
        $this->createOperation(2, 'Bill', 'Outflow', 0);

        $this->postJson('/api/transactions', $this->payload(1, 100))->assertOk();

        $response = $this->postJson('/api/transactions', $this->payload(2, 40));

        $response->assertOk()
            ->assertJsonPath('code', 'TRANSACTION_RECORDED')
            ->assertJsonPath('data.amount', 60);

        $this->assertSame(1, User::query()->count());
        $this->assertSame(60.0, (float) Client::query()->first()->amount);
    }

    public function testItReturnsNotFoundWhenTheOperationCodeDoesNotExist(): void
    {
        $response = $this->postJson('/api/transactions', $this->payload(99, 10));

        $response->assertStatus(422)
            ->assertJson([
                'data' => null,
                'message' => 'Operation not found.',
                'code' => 'OPERATION_NOT_FOUND',
                'status_code' => 422,
                'errors' => [],
            ]);

        $this->assertSame(0, Transaction::query()->count());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(int $type, float $value): array
    {
        return [
            'cpf' => '09620676017',
            'card' => '4753****3153',
            'date_at' => '20220901',
            'hour_at' => '153045',
            'name' => 'Ada',
            'store_name' => 'Acme',
            'type' => $type,
            'value' => $value,
        ];
    }

    private function createOperation(int $code, string $description, string $typeDescription, int $type): void
    {
        Operation::query()->create([
            'code_operation' => $code,
            'description' => $description,
            'type_description' => $typeDescription,
            'type' => $type,
        ]);
    }
}
