<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Transaction $resource
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transaction = $this->resource;

        $response = [];

        if ($transaction->origin_internal_account_id !== null) {
            $response['origin'] = [
                'id'      => $transaction->origin_internal_account_id,
                'balance' => $transaction->originInternalAccount->balance,
            ];
        }

        if ($transaction->destination_internal_account_id !== null) {
            $response['destination'] = [
                'id'      => $transaction->destination_internal_account_id,
                'balance' => $transaction->destinationInternalAccount->balance,
            ];
        }

        return $response;
    }
}
