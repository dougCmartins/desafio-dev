<?php

declare(strict_types=1);

namespace Domain\Client\Models;

use Domain\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Client extends Model
{
    protected $table = 'clients';

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'cpf',
        'card',
        'user_id',
        'amount',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $with = ['users', 'stores'];

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function stores(): HasOne
    {
        return $this->hasOne(Store::class, 'owner_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'client_id', 'id');
    }
}
