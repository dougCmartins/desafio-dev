<?php

declare(strict_types=1);

namespace Domain\Transaction\Models;

use Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

final class Transaction extends Model
{
    protected $table = 'transactions';

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'store_id',
        'type',
        'value',
        'date_at',
        'hour_at',
        'amount',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'store_id',
    ];

    /**
     * @var array<int, string>
     */
    protected $with = ['operations'];

    public function clients(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function operations(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'type', 'code_operation');
    }

    /**
     * @param mixed $value
     */
    public function setDateAtAttribute($value): void
    {
        $value = (string) $value;

        if (preg_match('/^\d{8}$/', $value) === 1) {
            $value = substr($value, 0, 4).'-'.substr($value, 4, 2).'-'.substr($value, 6, 2);
        }

        $this->attributes['date_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * @param mixed $value
     */
    public function setHourAtAttribute($value): void
    {
        $value = (string) $value;

        if (preg_match('/^\d{6}$/', $value) === 1) {
            $value = substr($value, 0, 2).':'.substr($value, 2, 2).':'.substr($value, 4, 2);
        }

        $this->attributes['hour_at'] = Carbon::parse($value)->format('H:i:s');
    }
}
