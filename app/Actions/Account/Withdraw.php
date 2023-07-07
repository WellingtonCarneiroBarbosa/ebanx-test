<?php

namespace App\Actions\Account;

use App\Actions\Account\Concerns\AsExistingAccountAction;
use App\Events\Transaction\NewWithdraw;
use App\Models\Transaction;
use DB;

class Withdraw
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
        try {
            DB::beginTransaction();

            $this->subtractAmountFromBalance();

            $transaction = $this->saveTransaction();

            event(new NewWithdraw($transaction));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $transaction;
    }

    protected function subtractAmountFromBalance(): void
    {
        $this->account
            ->decreaseBalance($this->amount)
            ->save();
    }

    protected function saveTransaction(): Transaction
    {
        $transaction = new Transaction();

        $transaction->amount                        = $this->amount;
        $transaction->origin_internal_account_id    = $this->account->id;
        $transaction->type                          = Transaction::TYPES['withdraw'];

        $transaction->save();

        return $transaction;
    }
}
