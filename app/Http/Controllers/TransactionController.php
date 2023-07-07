<?php

namespace App\Http\Controllers;

use App\Actions\Account\Create as CreateAccount;
use App\Actions\Account\Deposit;
use App\Actions\Account\Withdraw;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    protected array $data;

    public function __invoke(TransactionRequest $request): JsonResponse|Response
    {
        $data = $request->validated();

        $this->data = $data;

        switch($data['type']) {
            case Transaction::TYPES['deposit']:
                try {
                    $account = Account::findOrFail($data['destination']);

                    return $this->handleDepositRequest($account);
                } catch (ModelNotFoundException) {
                    return $this->handleCreateAccountRequest();
                }
            case Transaction::TYPES['withdraw']:
                try {
                    $account = Account::findOrFail($data['origin']);
                } catch (ModelNotFoundException) {
                    return response(0, Response::HTTP_NOT_FOUND);
                }

                return $this->handleWithdrawRequest($account);

            case Transaction::TYPES['transfer']:
                return $this->handleTransferRequest();
        }
    }

    protected function handleCreateAccountRequest(): JsonResponse
    {
        $data = $this->data;

        $account = (new CreateAccount())
            ->setId($data['destination'])
            ->setBalance($data['amount'])
            ->execute();

        return response()->json([
            'destination' => [
                'id'      => $account->id,
                'balance' => $account->balance,
            ],
        ], Response::HTTP_CREATED);
    }

    protected function handleDepositRequest(Account $destinationAccount): JsonResponse
    {
        (new Deposit())
            ->setAccount($destinationAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return response()->json([
            'destination' => [
                'id'      => $destinationAccount->id,
                'balance' => $destinationAccount->balance,
            ],
        ], Response::HTTP_CREATED);
    }

    protected function handleWithdrawRequest(Account $originAccount): JsonResponse
    {
        (new Withdraw())
            ->setAccount($originAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return response()->json([
            'origin' => [
                'id'      => $originAccount->id,
                'balance' => $originAccount->balance,
            ],
        ], Response::HTTP_CREATED);
    }

    protected function handleTransferRequest(): JsonResponse
    {
        return response()->json();
    }
}
