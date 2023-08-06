<?php

namespace App\Exceptions\Accounts;

use App\Models\Account;
use Exception;

class InsufficientFunds extends Exception
{
    public function __construct(public Account $account)
    {
        parent::__construct('InsufficientFunds funds. Minimum negative balance allowed: ' . config('accounts.minimum_balance'));
    }
}
