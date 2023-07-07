<?php

namespace Tests\Feature\Transactions;

use App\Events\Transaction\NewDeposit;
use App\Models\Account;
use App\Models\Transaction;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_increase_the_account_balance_when_a_deposit_is_made(): void
    {
        $account = Account::factory()->create();

        $data = [
            'type'        => Transaction::TYPES['deposit'],
            'destination' => $account->id,
            'amount'      => fake()->numberBetween(1, 1000000),
        ];

        $currentBalance = $account->balance;

        $response = $this->post(route('transaction'), $data);

        $this->assertDatabaseHas('transactions', [
            'destination_internal_account_id' => $account->id,
            'origin_internal_account_id'      => null,
            'amount'                          => $data['amount'],
            'type'                            => Transaction::TYPES['deposit'],
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'destination' => [
                'id'      => $account->id,
                'balance' => $account->refresh()->balance,
            ],
        ]);

        $this->assertEquals($account->refresh()->balance, $data['amount'] + $currentBalance);

        $currentBalance = $account->balance;

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $response->assertJson([
            'destination' => [
                'id'      => $account->id,
                'balance' => $account->refresh()->balance,
            ],
        ]);

        $this->assertEquals($account->refresh()->balance, $data['amount'] + $currentBalance);

        $this->assertDatabaseHas('transactions', [
            'destination_internal_account_id' => $account->id,
            'origin_internal_account_id'      => null,
            'amount'                          => $data['amount'],
            'type'                            => Transaction::TYPES['deposit'],
        ]);

        $this->assertDatabaseCount('transactions', 2);
    }

    /** @test */
    public function it_should_dispatch_new_deposit_event_when_a_deposit_is_made(): void
    {
        $account = Account::factory()->create();

        $data = [
            'type'        => Transaction::TYPES['deposit'],
            'destination' => $account->id,
            'amount'      => fake()->numberBetween(1, 1000000),
        ];

        Event::fake(NewDeposit::class);

        $this->post(route('transaction'), $data);

        Event::assertDispatched(NewDeposit::class, function (NewDeposit $event) use ($data) {
            return $event->transaction instanceof Transaction
                && $event->transaction->amount === $data['amount']
                && $event->transaction->type === Transaction::TYPES['deposit'];
        });
    }
}
