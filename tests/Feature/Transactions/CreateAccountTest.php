<?php

namespace Tests\Feature\Transactions;

use App\Events\Transaction\NewDeposit;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_create_an_account_if_there_is_no_account_with_destination_id_passed(): void
    {
        $data = [
            'type'        => 'deposit',
            'destination' => '100',
            'amount'      => 10,
        ];

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $response->assertJson([
            'destination' => [
                'id'      => '100',
                'balance' => 10,
            ],
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => 100,
            'balance' => 10,
        ]);
    }

    /** @test */
    public function it_should_not_create_an_account_if_there_is_an_account_with_destination_id_passed(): void
    {
        $data = [
            'type'        => 'deposit',
            'destination' => '100',
            'amount'      => 10,
        ];

        $this->post(route('transaction'), $data);

        $response = $this->post(route('transaction'), $data);
        $response = $this->post(route('transaction'), $data);
        $response = $this->post(route('transaction'), $data);
        $response = $this->post(route('transaction'), $data);

        // 201 because it is a deposit
        $response->assertStatus(201);

        $this->assertDatabaseCount('accounts', 1);
    }

    /** @test */
    public function assert_a_deposit_transaction_is_made_when_an_account_is_created(): void
    {
        $data = [
            'type'        => 'deposit',
            'destination' => '100',
            'amount'      => 10,
        ];

        Event::fake(NewDeposit::class);

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'destination_internal_account_id' => '100',
            'type'                            => Transaction::TYPES['deposit'],
            'amount'                          => 10,
        ]);

        Event::assertDispatched(NewDeposit::class, function (NewDeposit $event) use ($data) {
            return $event->transaction instanceof Transaction
                && $event->transaction->amount === $data['amount']
                && $event->transaction->type === Transaction::TYPES['deposit'];
        });
    }
}
