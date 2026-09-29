# 🐘 Backend Standards (PHP / Laravel & DDD)

## 1. Architecture, Domain-Driven Design (DDD) & Limites de Contexto
- **Domínios Bem Definidos:** A aplicação deve ser estruturada em domínios baseados em contextos de negócio (ex: `Users`, `Products`, `Sales`, `Auth`) e não por tipos genéricos de ficheiro (MVC tradicional).
- **Context Boundaries:** Serviços e Ações não devem conhecer ou mutar diretamente modelos de banco de dados pertencentes a domínios de negócio completamente distintos.
- **Regra de Isolamento (Actions & Models):** Uma `Action` tem a responsabilidade de executar uma única tarefa de negócio e **só pode aceder aos Models do seu próprio domínio**.
- **Proibido Cruzar Domínios Diretamente:** Uma `Action` **nunca** deve invocar uma `Action` pertencente a outro domínio.
- **Orquestradores (Orchestrators):** Quando um fluxo de negócio exigir interação entre múltiplos domínios (ex: criar uma venda, deduzir o stock do produto e notificar o utilizador), utilize um **Orchestrator**. Estes devem residir numa diretoria dedicada (`Orchestrators`) e atuar exclusivamente como uma ponte de orquestração, chamando as respetivas Actions de cada domínio.

### 📁 Estrutura de Pastas (DDD na prática)

```
src/
├── Domain/
│   ├── Sale/
│   │   ├── Actions/
│   │   │   └── CreateSale.php
│   │   ├── Models/
│   │   │   └── Sale.php
│   │   ├── Data/
│   │   │   ├── CreateSaleData.php
│   │   │   └── SaleData.php
│   │   ├── Enums/
│   │   │   └── SaleStatus.php
│   │   ├── Events/
│   │   │   └── SaleCreated.php
│   │   ├── Exceptions/
│   │   │   └── InvalidSaleException.php
│   │   ├── Controllers/
│   │   │   └── SaleController.php
│   │   ├── Routes/
│   │   │   └── api.php
│   │   └── Tests/
│   │       ├── Feature/
│   │       │   └── CreateSaleTest.php
│   │       └── Unit/
│   │           └── SaleModelTest.php
│   ├── Product/
│   │   ├── Actions/
│   │   │   └── DecrementStock.php
│   │   ├── Models/
│   │   │   └── Product.php
│   │   └── ...
│   └── User/
│       ├── Actions/
│       │   └── NotifyUser.php
│       └── ...
└── Orchestrators/
    └── Checkout/
        ├── Actions/
        │   └── ProcessCheckout.php
        ├── Data/
        │   └── CheckoutResultData.php
        └── Controllers/
            └── CheckoutController.php
```

### 🎯 Exemplo: Action dentro do seu domínio

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Actions;

use Domain\Sale\Data\CreateSaleData;
use Domain\Sale\Data\SaleData;
use Domain\Sale\Enums\SaleStatus;
use Domain\Sale\Events\SaleCreated;
use Domain\Sale\Exceptions\InvalidSaleException;
use Domain\Sale\Models\Sale;
use Illuminate\Support\Facades\DB;

final class CreateSale
{
    public function handle(CreateSaleData $data): SaleData
    {
        if ($data->amount <= 0) {
            throw new InvalidSaleException('O valor da venda deve ser positivo.');
        }

        $sale = DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'client_id' => $data->client_id,
                'amount'    => $data->amount,
                'status'    => SaleStatus::Pending,
            ]);

            SaleCreated::dispatch(SaleData::from($sale));

            return $sale;
        });

        return SaleData::from($sale);
    }
}
```

### 🎯 Exemplo: Orchestrator chamando múltiplos domínios

```php
<?php

declare(strict_types=1);

namespace Orchestrators\Checkout\Actions;

use Domain\Product\Actions\DecrementStock;
use Domain\Sale\Actions\CreateSale;
use Domain\Sale\Data\CreateSaleData;
use Domain\User\Actions\NotifyUser;
use Orchestrators\Checkout\Data\CheckoutResultData;

final class ProcessCheckout
{
    public function __construct(
        private readonly CreateSale $createSale,
        private readonly DecrementStock $decrementStock,
        private readonly NotifyUser $notifyUser,
    ) {}

    public function handle(CheckoutData $data): CheckoutResultData
    {
        // Orquestra: cada Action pertence ao seu domínio, o Orchestrator só coordena.
        $sale = $this->createSale->handle(
            new CreateSaleData(
                client_id: $data->client_id,
                amount: $data->amount,
            )
        );

        $this->decrementStock->handle($data->product_id, $data->quantity);
        $this->notifyUser->handle($data->client_id, 'Compra realizada com sucesso!');

        return new CheckoutResultData(saleId: $sale->id);
    }
}
```

---

## 2. Estrutura de Código, Tipagem e Testes
- **Strict Typing:** Declare obrigatoriamente tipos estritos no topo de todos os arquivos PHP (`declare(strict_types=1);`).
- **DTOs & Controladores Magros (Skinny Controllers):** Utilize DTOs (Data Transfer Objects) para encapsular payloads de requisições e transferência de dados entre camadas. Evite passar arrays primitivos profundamente na lógica de negócio. O Controller apenas recebe o pedido, converte para DTO e delega a execução para a Action ou Orchestrator.
- **Enums e Ficheiros de Configuração:** Utilize `Enums` nativos do PHP para definir estados, tipos de dados estritos ou categorias. Variáveis de ambiente, *flags* e parametrizações de integração devem estar externalizados em ficheiros de configuração (`config/`).
- **Testes no Escopo do Domínio:** Os testes automatizados (Pest/PHPUnit) não devem ficar isolados numa pasta global e genérica, mas sim organizados **dentro do escopo do seu próprio domínio**, garantindo que cada domínio é auto-testável e verdadeiramente modular.

### 🎯 Exemplo: Controller magro (Skinny Controller)

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Controllers;

use Domain\Sale\Actions\CreateSale;
use Domain\Sale\Data\CreateSaleData;
use Domain\Sale\Data\SaleData;
use Illuminate\Http\JsonResponse;

final class SaleController
{
    public function store(CreateSaleData $data, CreateSale $action): SaleData
    {
        // Controller não tem lógica. Só recebe o DTO (já validado) e delega.
        return $action->handle($data);
    }

    public function index(SaleIndexData $data): JsonResponse
    {
        // Em listagens, o Controller pode retornar uma coleção paginada.
        return SaleData::collect(
            Sale::query()->where('client_id', $data->client_id)->paginate()
        )->toResponse(request());
    }
}
```

### 🎯 Exemplo: Enum nativo do PHP

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Enums;

enum SaleStatus: string
{
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Canceled = 'canceled';
    case Refunded = 'refunded';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Canceled, self::Refunded => true,
            default                        => false,
        };
    }
}
```

### 🎯 Exemplo: Configuração externalizada (`config/sale.php`)

```php
<?php

// config/sale.php

return [
    'max_installments'   => env('SALE_MAX_INSTALLMENTS', 12),
    'min_amount'         => env('SALE_MIN_AMOUNT', 1.00),
    'auto_approve_below' => env('SALE_AUTO_APPROVE_BELOW', 50.00),
];
```

Uso na Action:

```php
if ($data->amount < config('sale.min_amount')) {
    throw new InvalidSaleException('Valor abaixo do mínimo permitido.');
}
```

### 🎯 Exemplo: Teste dentro do domínio (`Domain/Sale/Tests/Feature/CreateSaleTest.php`)

```php
<?php

use Domain\Sale\Actions\CreateSale;
use Domain\Sale\Data\CreateSaleData;
use Domain\Sale\Models\Sale;

it('cria uma venda com sucesso', function () {
    $data = new CreateSaleData(client_id: 1, amount: 100.00);

    $result = app(CreateSale::class)->handle($data);

    expect($result->amount)->toBe(100.00)
        ->and(Sale::count())->toBe(1);
});

it('rejeita venda com valor inválido', function () {
    $data = new CreateSaleData(client_id: 1, amount: 0);

    app(CreateSale::class)->handle($data);
})->throws(\Domain\Sale\Exceptions\InvalidSaleException::class);
```

---

## 3. Database & Security
- **Eloquent First:** Priorize sempre o Eloquent ORM para consultas ao banco de dados e definição de relacionamentos. Mantenha a complexidade de consultas isolada em *Scopes* ou *Query Builders* dedicados ao domínio.
- **Raw SQL Policy:** `DB::raw` ou consultas puras são estritamente restritas a agregações críticas de performance. Quando utilizadas, devem ser isoladas em repositórios/camadas de serviço, rigorosamente documentadas e utilizar placeholders (`?`) para prevenir SQL Injection.
- **Mass Assignment Protection:** Defina explicitamente `$fillable` ou `$guarded` em todos os modelos Eloquent. Nunca deixe `$guarded = []` sem proteção explícita.
- **Input Validation:** Valide rigorosamente todos os payloads de entrada utilizando `Form Requests` ou classes de validação centralizadas antes de qualquer processamento.

### 🎯 Exemplo: Model Eloquent com `$fillable`, `casts` e Scopes

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Models;

use Domain\Sale\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Sale extends Model
{
    protected $table = 'sales';

    // Mass Assignment Protection
    protected $fillable = [
        'client_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'status'  => SaleStatus::class,
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Scope de domínio — encapsula regra de negócio
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', SaleStatus::Paid->value);
    }

    public function scopeRecent(Builder $query, int $days = 25): Builder
    {
        return $query->where('paid_at', '>=', now()->subDays($days));
    }
}
```

### 🎯 Exemplo: Uso de `DB::raw` com placeholders (Raw SQL Policy)

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Queries;

use Illuminate\Support\Facades\DB;

final class SaleAggregateQuery
{
    /**
     * Agregação crítica de performance — isolada e documentada.
     * Usa placeholders (?) para evitar SQL Injection.
     */
    public function totalBySeller(int $sellerId, string $startDate): float
    {
        $result = DB::selectOne(
            'SELECT SUM(amount) AS total
             FROM sales
             WHERE seller_id = ?
               AND paid_at >= ?',
            [$sellerId, $startDate]
        );

        return (float) ($result->total ?? 0);
    }
}
```

---

## 4. API, Respostas e Observabilidade
- **API Resources:** Utilize `JsonResource` ou `API Resources` para formatar as respostas JSON retornadas aos clientes, desacoplando a estrutura do banco de dados da representação da API.
- **Padrão de Resposta da API (Envelope Pattern):** O frontend deve receber respostas estandardizadas (sucesso ou erro) através de um "Envelope". A estrutura JSON de resposta deve obrigatoriamente conter as seguintes chaves:

```json
{
  "data": { ... },
  "message": "Mensagem amigável descritiva",
  "code": "TAG_DO_EVENTO_OU_ERRO",
  "status_code": 200,
  "errors": []
}
```

- **O poder da chave `code`:** O campo `code` atua como uma *tag* única (ex: `USER_CREATED`, `PRODUCT_OUT_OF_STOCK`), permitindo ao frontend mapear lógicas de UI e traduções sem depender de texto puro ou do `status_code` HTTP de forma isolada.
- **Exception Handling e Exceções Semânticas (Domain Exceptions):** Lance exceções com nomes claros de negócio (ex: `InvalidCartException`). Trate exceções de forma centralizada no `Handler` global, que deve capturá-las e formatá-las automaticamente dentro do Envelope Padrão da API com os códigos HTTP apropriados.
- **Logs Estruturados:** Nunca utilize o log de forma silenciosa ou apenas com strings genéricas. Ao processar dados críticos, adicione logs estruturados passando arrays de contexto (`Log::info('Venda processada com sucesso', ['order_id' => $id, 'user_id' => $userId])`).

### 🎯 Exemplo: Domain Exception semântica

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Exceptions;

use DomainException;

final class InvalidSaleException extends DomainException
{
    public function __construct(string $message = 'Venda inválida.')
    {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return 'INVALID_SALE';
    }

    public function getHttpStatus(): int
    {
        return 422;
    }
}
```

### 🎯 Exemplo: Exception Handler global formatando o Envelope

```php
<?php

// app/Exceptions/Handler.php (ou bootstrap/app.php no Laravel 11)

use Domain\Sale\Exceptions\InvalidSaleException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;

public function render($request, Throwable $e): JsonResponse
{
    if ($e instanceof InvalidSaleException) {
        return response()->json([
            'data'        => null,
            'message'     => $e->getMessage(),
            'code'        => $e->getErrorCode(),
            'status_code' => $e->getHttpStatus(),
            'errors'      => [],
        ], $e->getHttpStatus());
    }

    // Fallback genérico — nunca expor stack trace em produção
    return response()->json([
        'data'        => null,
        'message'     => 'Erro interno.',
        'code'        => 'INTERNAL_ERROR',
        'status_code' => 500,
        'errors'      => config('app.debug') ? [$e->getMessage()] : [],
    ], 500);
}
```

### 🎯 Exemplo: API Resource + Envelope de sucesso

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class SaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'client_id'  => $this->client_id,
            'amount'     => $this->amount,
            'status'     => $this->status->value,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }

    public function with($request): array
    {
        return [
            'message'     => 'Venda criada com sucesso.',
            'code'        => 'SALE_CREATED',
            'status_code' => 201,
            'errors'      => [],
        ];
    }
}
```

Resposta JSON resultante:

```json
{
  "data": {
    "id": 42,
    "client_id": 1,
    "amount": "100.00",
    "status": "pending",
    "created_at": "2026-09-29T12:00:00+00:00"
  },
  "message": "Venda criada com sucesso.",
  "code": "SALE_CREATED",
  "status_code": 201,
  "errors": []
}
```

### 🎯 Exemplo: Logs estruturados

```php
<?php

declare(strict_types=1);

namespace Domain\Sale\Actions;

use Illuminate\Support\Facades\Log;

final class CreateSale
{
    public function handle(CreateSaleData $data): SaleData
    {
        Log::info('Iniciando criação de venda', [
            'client_id' => $data->client_id,
            'amount'    => $data->amount,
        ]);

        try {
            // ... lógica
            $sale = Sale::create([...]);

            Log::info('Venda criada com sucesso', [
                'sale_id'   => $sale->id,
                'client_id' => $sale->client_id,
                'amount'    => $sale->amount,
            ]);

            return SaleData::from($sale);
        } catch (\Throwable $e) {
            Log::error('Falha ao criar venda', [
                'client_id' => $data->client_id,
                'amount'    => $data->amount,
                'error'     => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
```

---

## 5. Legacy Refactoring & Migrations
- Ao refatorar código legado ou migrar estruturas de banco de dados, preserve a retrocompatibilidade quando aplicável e garanta que as regras de negócio permaneçam intactas.
- Evite introduzir complexidade arquitetural excessiva (over-engineering) quando uma refatoração simples atinge o requisito com excelência.

### 🎯 Exemplo: Migration retrocompatível

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Adiciona coluna nova como nullable para não quebrar registros existentes
            $table->string('payment_method')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });

        // Popula valores padrão para registros antigos
        DB::table('sales')->whereNull('payment_method')->update([
            'payment_method' => 'unknown',
        ]);
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'paid_at']);
        });
    }
};
```

### 🎯 Exemplo: Refatoração incremental — de Controller gordo para Action

**Antes (legado):**

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'client_id' => 'required|exists:clients,id',
        'amount'    => 'required|numeric|min:1',
    ]);

    $sale = Sale::create($validated);
    event(new SaleCreated($sale));

    return response()->json($sale, 201);
}
```

**Depois (refatorado, com DTO + Action + Resource):**

```php
public function store(CreateSaleData $data, CreateSale $action): SaleResource
{
    return new SaleResource($action->handle($data));
}
```

A refatoração preserva a regra de negócio (validar cliente, criar venda, disparar evento) e adiciona tipagem, separação de camadas e envelope de resposta — sem introduzir complexidade desnecessária.

---

## 📋 Tabela Resumo – Onde cada peça vive

| Camada / Artefato | Localização | Responsabilidade |
|-------------------|-------------|-------------------|
| **Model** | `Domain/{X}/Models/` | Entidade, relacionamentos, scopes |
| **Action** | `Domain/{X}/Actions/` | Uma operação de negócio |
| **Data (DTO)** | `Domain/{X}/Data/` | Contrato de entrada/saída, validação |
| **Enum** | `Domain/{X}/Enums/` | Estados, tipos, categorias |
| **Event** | `Domain/{X}/Events/` | Side effects entre módulos |
| **Exception** | `Domain/{X}/Exceptions/` | Erros de domínio semânticos |
| **Controller** | `Domain/{X}/Controllers/` | Orquestra DTO + Action |
| **Resource** | `Domain/{X}/Resources/` | Formatação de resposta JSON |
| **Routes** | `Domain/{X}/Routes/api.php` | Rotas do domínio |
| **Tests** | `Domain/{X}/Tests/` | Testes do domínio |
| **Orchestrator** | `Orchestrators/{Y}/` | Coordena múltiplos domínios |

---

## ✅ Checklist final do Backend

- [ ] Todo arquivo PHP tem `declare(strict_types=1);`
- [ ] Actions são `final` e têm um único método `handle()`
- [ ] DTOs são `final`, readonly e validam entrada
- [ ] Models têm `$fillable` ou `$guarded` explícitos
- [ ] Controllers são magros — só delegam
- [ ] Exceções de domínio herdam de `DomainException`
- [ ] Handler global formata o envelope padrão
- [ ] Respostas usam `code` (SCREAMING_SNAKE_CASE)
- [ ] Logs são estruturados (com array de contexto)
- [ ] Testes ficam dentro do domínio (`Domain/{X}/Tests/`)
- [ ] Orchestrators coordenam múltiplos domínios, sem Models próprios
- [ ] Raw SQL só é usado com placeholders e documentado

Esse documento consolida as práticas de arquitetura backend adotadas em projetos Laravel com foco em DDD pragmático, tipagem estrita, separação de camadas e observabilidade — servindo como referência para qualquer desenvolvedor que queira aplicar esses padrões de forma consistente.