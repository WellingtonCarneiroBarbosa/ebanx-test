<?php

namespace App\Http\Controllers;

use App\Actions\Account\Create as CreateAccount;
use App\Actions\Account\Deposit;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    protected array $data;

    public function __invoke(TransactionRequest $request): JsonResponse
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
                return $this->handleWithdrawRequest();

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

    protected function handleWithdrawRequest(): JsonResponse
    {
        return response()->json();
    }

    protected function handleTransferRequest(): JsonResponse
    {
        return response()->json();
    }
}
