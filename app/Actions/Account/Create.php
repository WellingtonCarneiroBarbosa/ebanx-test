<?php

namespace App\Actions\Account;

use App\Models\Account;
use DB;

class Create
{
    protected int $id;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function execute(): Account
    {
        try {
            DB::beginTransaction();

            $account = new Account();

            $account->id      = $this->id;
            $account->balance = 0;

            $account->save();

            DB::commit();

            return $account;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
