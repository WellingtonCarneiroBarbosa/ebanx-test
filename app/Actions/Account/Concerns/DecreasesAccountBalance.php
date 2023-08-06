<?php

namespace App\Actions\Account\Concerns;

use App\Models\Account;

trait DecreasesAccountBalance
{
    protected Account $originAccount;

    protected int $originalOriginAccountBalance;

    protected int $amount;

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setOriginAccount(Account $originAccount): self
    {
        $this->originAccount                = $originAccount;
        $this->originalOriginAccountBalance = $originAccount->balance;

        return $this;
    }

    protected function canDecreaseBalance(): bool
    {
        $this->originAccount->decreaseBalance($this->amount);

        $passes = false;

        if ($this->originAccount->balance >= config('accounts.minimum_balance')) {
            $passes = true;
        }

        $this->originAccount->balance = $this->originalOriginAccountBalance;

        return $passes;
    }
}
