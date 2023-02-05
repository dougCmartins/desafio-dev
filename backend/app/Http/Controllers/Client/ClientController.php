<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * @var Client|null
     */
    protected $client = null;

    /**
     * @param Client $client
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = $this->client->with(['users', 'transactions', 'stores'])
            ->get()
            ->toArray() ?? [];

        return response($clients, 200)
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
        $clients = $this->client->find($id) ?? [];
        return response($clients, 200)
            ->header('Content-Type', 'text/plain');
    }
}
