<?php

namespace App\Actions\Account;

use App\Models\Account;

class Create
{
    protected int $id;

    protected int $balance = 0;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setBalance(int $amount): self
    {
        $this->balance = $amount;

        return $this;
    }

    public function execute(): Account
    {
        $account = new Account();

        $account->id      = $this->id;
        $account->balance = $this->balance;

        $account->save();

        return $account;
    }
}