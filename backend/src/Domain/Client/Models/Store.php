<?php

declare(strict_types=1);

namespace Domain\Client\Models;

use Domain\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Store extends Model
{
    protected $table = 'stores';

    protected $primaryKey = 'id';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'owner_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @param mixed $value
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucfirst(strtolower((string) $value));
    }

    public function clients(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'owner_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'client_id', 'id');
    }
}
