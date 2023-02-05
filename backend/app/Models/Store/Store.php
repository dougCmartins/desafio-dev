<?php

namespace App\Models\Store;

use App\Models\Client\Client;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'stores';

    /**
     * @inheritDoc
     */
    protected $primaryKey = 'id';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'owner_id',
        'created_at',
        'updated_at'
    ];

    /**
     * @param $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }

    /**
     * @return BelongsTo
     */
    public function clients() {
        return $this->belongsTo(Client::class, 'owner_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function transactions() {
        return $this->hasMany(Transaction::class, 'client_id', 'id');
    }
}
