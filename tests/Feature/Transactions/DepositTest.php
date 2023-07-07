<?php

namespace Tests\Feature\Transactions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_increase_the_account_balance_when_a_deposit_event_is_made(): void
    {
        $account = Account::factory()->create();

        $data = [
            'type'        => Transaction::TYPES['deposit'],
            'destination' => $account->id,
            'amount'      => fake()->numberBetween(1, 1000000),
        ];

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
    }
}
