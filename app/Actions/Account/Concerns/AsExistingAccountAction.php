<?php

namespace App\Actions\Account\Concerns;

use App\Models\Account;

trait AsExistingAccountAction
{
    protected Account $account;

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }
}
