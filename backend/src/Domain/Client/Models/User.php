<?php

declare(strict_types=1);

namespace Domain\Client\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

final class User extends Authenticatable
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function clients(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'user_id', 'id');
    }
}
