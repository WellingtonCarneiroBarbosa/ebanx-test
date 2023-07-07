<?php

namespace App\Http\Controllers;

use App\Actions\Account\Create as CreateAccount;
use App\Actions\Account\Deposit;
use App\Actions\Account\Transfer;
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
                try {
                    $destinationAccount = Account::findOrFail($data['destination']);
                } catch(ModelNotFoundException) {
                    $destinationAccount = $this->createAccount(
                        $data['destination'],
                        0
                    );
                }

                try {
                    $originAccount = Account::findOrFail($data['origin']);
                } catch (ModelNotFoundException) {
                    return response(0, Response::HTTP_NOT_FOUND);
                }

                return $this->handleTransferRequest(
                    $originAccount,
                    $destinationAccount,
                );
        }
    }

    protected function handleCreateAccountRequest(): JsonResponse
    {
        $data = $this->data;

        $account = $this->createAccount(
            $data['destination'],
            $data['amount']
        );

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

    protected function handleTransferRequest(
        Account $originAccount,
        Account $destinationAccount
    ): JsonResponse {
        (new Transfer())
            ->setOriginAccount($originAccount)
            ->setDestinationAccount($destinationAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return response()->json([
            'origin' => [
                'id'      => $originAccount->id,
                'balance' => $originAccount->balance,
            ],

            'destination' => [
                'id'      => $destinationAccount->id,
                'balance' => $destinationAccount->balance,
            ],
        ], Response::HTTP_CREATED);
    }

    private function createAccount(
        int $id,
        int $initialAmount = 0,
    ): Account {
        return (new CreateAccount())
            ->setId($id)
            ->setBalance($initialAmount)
            ->execute();
    }
}
