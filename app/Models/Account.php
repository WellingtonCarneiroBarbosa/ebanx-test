<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Account
 *
 * @property string $id
 * @property string $uuid
 * @property float $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUuid($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'id'      => 'string',
        'balance' => 'float',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_internal_account_id')
            ->union($this->hasMany(Transaction::class, 'origin_internal_account_id'));
    }

    public function increaseBalance(int $amount): self
    {
        $this->balance += $amount;

        return $this;
    }

    public function decreaseBalance(int $amount): self
    {
        $this->balance -= $amount;

        return $this;
    }
}
