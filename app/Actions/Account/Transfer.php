<?php

namespace App\Actions\Account;

use App\Actions\Account\Concerns\DecreasesAccountBalance;
use App\Events\Account\TransferReceived;
use App\Events\Account\TransferSended;
use App\Exceptions\Accounts\InsufficientFunds;
use App\Models\Account;
use App\Models\Transaction;
use DB;

class Transfer
{
    use DecreasesAccountBalance;

    protected Account $originAccount;

    protected Account $destinationAccount;

    public function setDestinationAccount(Account $destinationAccount): self
    {
        $this->destinationAccount = $destinationAccount;

        return $this;
    }

    public function execute(): Transaction
    {
        try {
            DB::beginTransaction();

            throw_if(!$this->canDecreaseBalance(), new InsufficientFunds($this->originAccount));

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
