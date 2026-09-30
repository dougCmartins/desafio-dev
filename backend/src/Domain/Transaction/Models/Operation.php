<?php

declare(strict_types=1);

namespace Domain\Transaction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Operation extends Model
{
    public const INFLOW = 1;

    public const OUTFLOW = 0;

    protected $table = 'operations';

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code_operation',
        'description',
        'type_description',
        'type',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'type', 'code_operation');
    }
}
