<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\LowerCaseCast;
use App\Models\Concerns\MonetaryCast;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @property string $id
 * @property string $uuid
 * @property LowerCaseCast $type
 * @property int $destination_internal_account_id
 * @property MonetaryCast $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDestinationInternalAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUuid($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasUuid;

    public const TYPES = [
        'deposit'  => 'deposit',
        'withdraw' => 'withdraw',
        'transfer' => 'transfer',
    ];

    protected $casts = [
        'id'     => 'string',
        'type'   => LowerCaseCast::class,
        'amount' => MonetaryCast::class,
    ];
}
