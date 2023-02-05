<?php

namespace App\Models\Client;

use App\Models\Store\Store;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'clients';

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
        'cpf',
        'card',
        'user_id',
        'amount',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['users', 'stores'];

    /**
     * @return HasOne
     */
    public function users() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function stores() {
        return $this->hasOne(Store::class, 'owner_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function transactions() {
        return $this->hasMany(Transaction::class, 'client_id', 'id');
    }
}
