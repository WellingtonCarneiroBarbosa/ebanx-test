<?php

namespace Tests\Feature\Transactions;

use App\Events\Transaction\NewWithdraw;
use App\Models\Account;
use App\Models\Transaction;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_decrease_the_account_balance_when_a_withdraw_is_made(): void
    {
        $account = Account::factory()->create([
            'balance' => 1000,
        ]);

        $data = [
            'type'   => Transaction::TYPES['withdraw'],
            'amount' => 100,
            'origin' => $account->id,
        ];

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $response->assertExactJson([
            'origin' => [
                'id'      => $account->id,
                'balance' => 900,
            ],
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => $account->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('transactions', [
            'origin_internal_account_id'      => $account->id,
            'destination_internal_account_id' => null,
            'amount'                          => 100,
            'type'                            => Transaction::TYPES['withdraw'],
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseCount('accounts', 1);
    }

    /** @test */
    public function it_should_emit_new_withdraw_event_when_a_withdraw_is_made(): void
    {
        $account = Account::factory()->create();

        $data = [
            'type'   => Transaction::TYPES['withdraw'],
            'amount' => 100,
            'origin' => $account->id,
        ];

        Event::fake(NewWithdraw::class);

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        Event::assertDispatched(NewWithdraw::class, function (NewWithdraw $event) {
            return $event->transaction instanceof Transaction
                && $event->transaction->amount === 100.0
                && $event->transaction->type === Transaction::TYPES['withdraw'];
        });
    }
}
