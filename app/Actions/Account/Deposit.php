<?php

namespace App\Actions\Account;

use App\Actions\Account\Concerns\AsExistingAccountAction;
use App\Events\Transaction\NewDeposit;
use App\Models\Transaction;

class Deposit
{
    use AsExistingAccountAction;

    protected int $amount;

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function execute(): Transaction
    {
        $this->increaseAccountBalance();

        $transaction = $this->saveTransaction();

        event(new NewDeposit($transaction));

        return $transaction;
    }

    private function increaseAccountBalance(): void
    {
        $this->account
            ->increaseBalance($this->amount)
            ->save();
    }

    private function saveTransaction(): Transaction
    {
        $transaction = new Transaction();

        $transaction->amount                          = $this->amount;
        $transaction->destination_internal_account_id = $this->account->id;
        $transaction->type                            = Transaction::TYPES['deposit'];

        $transaction->save();

        return $transaction;
    }
}
