<?php

namespace App\Http\Controllers;

use App\Actions\Account\Create as CreateAccount;
use App\Actions\Account\Deposit;
use App\Actions\Account\Transfer;
use App\Actions\Account\Withdraw;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
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
        $this->data = $request->validated();

        switch($this->data['type']) {
            case Transaction::TYPES['deposit']:
                return $this->depositTypeRequest();

            case Transaction::TYPES['withdraw']:
                return $this->withdrawTypeRequest();

            case Transaction::TYPES['transfer']:
                return $this->transferTypeRequest();

            default:
                return apiResponse([
                    'message' => 'Invalid transaction type.',
                ], Response::HTTP_BAD_REQUEST);
        }
    }

    protected function depositTypeRequest(): JsonResponse|Response
    {
        try {
            $account = Account::findOrFail($this->data['destination']);

            return $this->handleDepositRequest($account);
        } catch (ModelNotFoundException) {
            return $this->handleCreateAccountRequest();
        }
    }

    protected function withdrawTypeRequest(): JsonResponse|Response
    {
        try {
            $account = Account::findOrFail($this->data['origin']);
        } catch (ModelNotFoundException) {
            return apiResponse(0, Response::HTTP_NOT_FOUND);
        }

        return $this->handleWithdrawRequest($account);
    }

    protected function transferTypeRequest(): JsonResponse|Response
    {
        $data = $this->data;

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
            return apiResponse(0, Response::HTTP_NOT_FOUND);
        }

        return $this->handleTransferRequest(
            $originAccount,
            $destinationAccount,
        );
    }

    protected function handleCreateAccountRequest(): JsonResponse
    {
        $data = $this->data;

        $account = $this->createAccount(
            $data['destination'],
            $data['amount']
        );

        return apiResponse([
            'destination' => [
                'id'      => $account->id,
                'balance' => $account->balance,
            ],
        ], Response::HTTP_CREATED);
    }

    protected function handleDepositRequest(Account $destinationAccount): JsonResponse
    {
        $transaction = (new Deposit())
            ->setAccount($destinationAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return apiResponse(new TransactionResource($transaction), Response::HTTP_CREATED);
    }

    protected function handleWithdrawRequest(Account $originAccount): JsonResponse
    {
        $transaction = (new Withdraw())
            ->setAccount($originAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return apiResponse(new TransactionResource($transaction), Response::HTTP_CREATED);
    }

    protected function handleTransferRequest(
        Account $originAccount,
        Account $destinationAccount
    ): JsonResponse {
        $transaction = (new Transfer())
            ->setOriginAccount($originAccount)
            ->setDestinationAccount($destinationAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return apiResponse(new TransactionResource($transaction), Response::HTTP_CREATED);
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
