<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\LowerCaseCast;
use App\Models\Concerns\MonetaryCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @property LowerCaseCast $type
 * @property MonetaryCast $amount
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory;
    use HasUuid;

    public const TYPES = [
        'deposit'           => 'deposit',
        'withdraw'          => 'withdraw',
        'internal_transfer' => 'internal_transfer',
        'external_transfer' => 'external_transfer',
    ];

    protected $casts = [
        'type'   => LowerCaseCast::class,
        'amount' => MonetaryCast::class,
    ];
}
