<?php

namespace App\Models\Transaction;

use App\Models\Client\Client;
use App\Models\Operation\Operation;
use App\Models\Store\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'transactions';

    /**
     * @inheritDoc
     */
    protected $primaryKey = 'id';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'store_id',
        'type',
        'value',
        'date_at',
        'hour_at',
        'amount'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'client_id',
        'store_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['operations'];


    /**
     * @return BelongsTo
     */
    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function stores() {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function operations() {
        return $this->belongsTo(Operation::class, 'type', 'code_operation');
    }

    /**
     * @param $value
     * @return void
     */
    public function setDateAtAttribute($value)
    {
        $this->attributes['date_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * @param $value
     * @return void
     */
    public function setHourAtAttribute($value)
    {
        $this->attributes['hour_at'] = Carbon::parse($value)->format('H:i:s');
    }
}
