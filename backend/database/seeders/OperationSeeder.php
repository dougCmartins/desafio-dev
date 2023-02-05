<?php

namespace Database\Seeders;

use App\Models\Operation\Operation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationSeeder extends Seeder
{
    public function run () {
        DB::transaction(function () {
            $records = [
                [
                    'code_operation' => 1,
                    'description' => 'Débito',
                    'type_description' => 'Entrada',
                    'type' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 2,
                    'description' => 'Boleto',
                    'type_description' => 'Saída',
                    'type' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 3,
                    'description' => 'Financiamento',
                    'type_description' => 'Saída',
                    'type' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 4,
                    'description' => 'Crédito',
                    'type_description' => 'Entrada',
                    'type' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 5,
                    'description' => 'Recebimento Empréstimo',
                    'type_description' => 'Entrada',
                    'type' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 6,
                    'description' => 'Vendas',
                    'type_description' => 'Entrada',
                    'type' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 7,
                    'description' => 'Recebimento TED',
                    'type_description' => 'Entrada',
                    'type' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 8,
                    'description' => 'Recebimento DOC',
                    'type_description' => 'Entrada',
                    'type' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'code_operation' => 9,
                    'description' => 'Aluguel',
                    'type_description' => 'Saída',
                    'type' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
            ];

            foreach ($records as $record) {
                Operation::query()->create($record);
            }
        });
    }

}
