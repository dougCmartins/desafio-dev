<?php

namespace App\Models\Operation;

use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'operations';

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


    public const TYPEVALUE = ['entrada' => 1, 'saída' => 0];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'code_operation',
        'description',
        'operator',
        'type_description',
         'type',
    ];

    /**
     * @return HasMany
     */
    public function transactions() {
        return $this->hasMany(Transaction::class, 'type', 'code_operation');
    }
}
