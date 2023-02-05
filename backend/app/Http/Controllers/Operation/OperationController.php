<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Models\Operation\Operation;

class OperationController extends Controller
{
    /**
     * @var Operation|null
     */
    protected $operation = null;

    /**
     * @param Operation $operation
     */
    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operations = $this->operation->all() ?? [];
        return response($operations, 200)
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
        $operation = $this->operation->find($id) ?? [];
        return response($operation, 200)
            ->header('Content-Type', 'text/plain');
    }
}
