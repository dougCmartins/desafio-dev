<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Operation\Operation;
use App\Models\Store\Store;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;

class TransactionController extends Controller
{
    /**
     * @var Transaction|null
     */
    protected $transaction = null;

    /**
     * @var Store|null
     */
    protected $store = null;

    /**
     * @var Client|null
     */
    protected $client = null;

    /**
     * @var User|null
     */
    protected $user = null;

    /**
     * @var Operation|null
     */
    protected $operation = null;

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction, Store $store, Client $client, User $user, Operation $operation)
   {
       $this->transaction = $transaction;
       $this->store = $store;
       $this->client = $client;
       $this->user = $user;
       $this->operation = $operation;
   }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = $this->transaction->with(['clients', 'stores', 'operations'])
            ->get()
            ->toArray() ?? [];

        return response($transactions, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = $this->transaction->find($id) ?? [];
        return response($transaction, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $transaction = $request->toArray();
            $validator = $this->isValid($transaction);

            if ($validator->fails()) {
                return response($validator->getMessageBag()->toArray(), 404)
                    ->header('Content-Type', 'text/plain');
            }

            $client = $this->findClientByCPF($transaction['cpf'])
                ->first();

            if (!$client) {
                return $this->createClientAndTransaction($transaction);
            }

            $item = $client->toArray();
            if ($item) {
                $transaction['client_id'] = $item['id'];
                $transaction['store_id'] = $item['stores']['id'];
                $operation = $this->findOperationByCode($transaction['type']);
                $balance = $this->amount($operation->getAttribute('type'), $item['amount'], $transaction['value']);

                $transaction['amount'] = $balance;
                $this->transaction->fill($transaction)->save();
                $client->update(['amount' => $balance]);
            }
            return $this->index();
        } catch (Exception $e) {
            return response(['response' => 'Could not create transaction'], 200)
                ->header('Content-Type', 'text/plain');
        }
    }


    /**
     * @param int $typeOperation
     * @param float $old
     * @param float $new
     * @return float
     */
    private function amount(int $typeOperation, float $old, float $new)
    {
        if ($typeOperation === $this->operation::TYPEVALUE['saída']) {
            return $old - $new;
        }

        return $old + $new;
    }


    /**
     * @param array $transaction
     * @return Response
     */
    public function createClientAndTransaction(array $transaction)
    {
        if ($transaction) {
            $transaction['amount'] = $transaction['value'];
            $transaction['user_id'] = $this->createItem($this->user, $transaction, 'user_id');
            $transaction['owner_id'] = $this->createItem($this->client, $transaction, 'owner_id');
            $transaction['client_id'] = $this->createItem($this->user, $transaction, 'client_id');
            $transaction['store_id'] =  $this->createItem($this->store, $transaction, 'store_id');

            $this->transaction->fill($transaction)->save();
        }
        return $this->index();
    }

    /**
     * @param $code
     */
    public function findOperationByCode($code) {
        return $this->operation->newQuery()
            ->where('code_operation', $code)
            ->first();
    }

    /**
     * @param string $cpf
     * @return Builder|array
     */
    public function findClientByCPF(string $cpf) {
        return $this->client->newQuery()
            ->where('cpf', '=', $cpf)
            ->get() ?? [];
    }

    /**
     * @param Model $model
     * @param array $transaction
     * @param string $fieldMerge
     * @return array|string
     */
    private function createItem(Model $model, array $transaction, string $fieldMerge) {
        if ($transaction) {
            $item = $model->fill($transaction);
            $item->save();
            $transaction[$fieldMerge] = $item->getKey();
        }

        return $transaction[$fieldMerge] ?? '';
    }

    /**
     * @param array $transaction
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function isValid(array $transaction): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($transaction, $this->rules());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    private function rules()
    {
        return [
            'cpf' => 'required|string',
            "card" =>'required|string',
            "date_at" =>'required|date',
            "hour_at" =>'required|string',
            "name" =>'required|string',
            "store_name" =>'required|string',
            "type" =>'required|int',
            "value" =>'required|numeric',
        ];
    }
}
