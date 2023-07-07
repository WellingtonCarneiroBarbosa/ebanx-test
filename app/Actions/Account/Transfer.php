<?php

namespace App\Actions\Account;

use App\Events\Account\TransferReceived;
use App\Events\Account\TransferSended;
use App\Models\Account;
use App\Models\Transaction;
use DB;

class Transfer
{
    protected Account $originAccount;

    protected Account $destinationAccount;

    protected int $amount;

    public function setOriginAccount(Account $originAccount): self
    {
        $this->originAccount = $originAccount;

        return $this;
    }

    public function setDestinationAccount(Account $destinationAccount): self
    {
        $this->destinationAccount = $destinationAccount;

        return $this;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function execute(): Transaction
    {
        try {
            DB::beginTransaction();

            $this->originAccount
                ->decreaseBalance($this->amount)
                ->save();

            $this->destinationAccount
                ->increaseBalance($this->amount)
                ->save();

            $transaction = $this->createTransaction();

            event(new TransferReceived($this->destinationAccount, $transaction));

            event(new TransferSended($this->originAccount, $transaction));

            DB::commit();

            return $transaction;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }

    protected function createTransaction(): Transaction
    {
        $transaction = new Transaction();

        $transaction->type                            = Transaction::TYPES['transfer'];
        $transaction->amount                          = $this->amount;
        $transaction->origin_internal_account_id      = $this->originAccount->id;
        $transaction->destination_internal_account_id = $this->destinationAccount->id;

        $transaction->save();

        return $transaction;
    }
}
