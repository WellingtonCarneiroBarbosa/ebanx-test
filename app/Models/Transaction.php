<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\LowerCaseCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Transaction
 *
 * @property string $id
 * @property string $uuid
 * @property LowerCaseCast $type
 * @property string|null $origin_internal_account_id
 * @property string|null $destination_internal_account_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account|null $destinationInternalAccount
 * @property-read \App\Models\Account|null $originInternalAccount
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDestinationInternalAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereOriginInternalAccountId($value)
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
        'id'                              => 'string',
        'origin_internal_account_id'      => 'string',
        'destination_internal_account_id' => 'string',
        'amount'                          => 'float',
        'type'                            => LowerCaseCast::class,
    ];

    public function originInternalAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'origin_internal_account_id');
    }

    public function destinationInternalAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_internal_account_id');
    }
}
