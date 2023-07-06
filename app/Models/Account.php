<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\MonetaryCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Account
 *
 * @property MonetaryCast $balance
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @mixin \Eloquent
 */
class Account extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'balance' => MonetaryCast::class,
    ];
}
