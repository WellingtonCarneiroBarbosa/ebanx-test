<?php

namespace App\Http\Controllers;

use App\Actions\Account\Create;
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
        return $this->makeDeposit(
            $this->findOrCreateAccount($this->data['destination'])
        );
    }

    protected function withdrawTypeRequest(): JsonResponse|Response
    {
        try {
            $account = Account::findOrFail($this->data['origin']);
        } catch (ModelNotFoundException) {
            return apiResponse(0, Response::HTTP_NOT_FOUND);
        }

        return $this->makeWithdraw($account);
    }

    protected function transferTypeRequest(): JsonResponse|Response
    {
        $data = $this->data;

        try {
            $originAccount = Account::findOrFail($data['origin']);
        } catch (ModelNotFoundException) {
            return apiResponse(0, Response::HTTP_NOT_FOUND);
        }

        return $this->makeTransfer(
            $originAccount,
            destinationAccount: $this->findOrCreateAccount($data['destination']),
        );
    }

    protected function makeDeposit(Account $destinationAccount): JsonResponse
    {
        $transaction = (new Deposit())
            ->setAccount($destinationAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return apiResponse(new TransactionResource($transaction), Response::HTTP_CREATED);
    }

    protected function makeWithdraw(Account $originAccount): JsonResponse
    {
        $transaction = (new Withdraw())
            ->setAccount($originAccount)
            ->setAmount($this->data['amount'])
            ->execute();

        return apiResponse(new TransactionResource($transaction), Response::HTTP_CREATED);
    }

    protected function makeTransfer(
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

    private function findOrCreateAccount(
        int $id,
    ): Account {
        try {
            $account = Account::findOrFail($id);
        } catch(ModelNotFoundException) {
            $account = (new Create())
                ->setId($id)
                ->execute();
        }

        return $account;
    }
}
