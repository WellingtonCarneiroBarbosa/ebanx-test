<?php

namespace Tests\Feature\Transactions;

use App\Events\Account\TransferReceived;
use App\Events\Account\TransferSended;
use App\Models\Account;
use App\Models\Transaction;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_decrease_origin_and_increase_destination_account(): void
    {
        $originAccount = Account::factory()->create([
            'balance' => 1000,
        ]);

        $destinationAccount = Account::factory()->create([
            'balance' => 0,
        ]);

        $data = [
            'type'        => Transaction::TYPES['transfer'],
            'origin'      => $originAccount->id,
            'destination' => $destinationAccount->id,
            'amount'      => 100,
        ];

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $response->assertExactJson([
            'origin' => [
                'id'      => $originAccount->id,
                'balance' => 900,
            ],
            'destination' => [
                'id'      => $destinationAccount->id,
                'balance' => 100,
            ],
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => $originAccount->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => $destinationAccount->id,
            'balance' => 100,
        ]);

        $this->assertDatabaseHas('transactions', [
            'type'                            => Transaction::TYPES['transfer'],
            'origin_internal_account_id'      => $originAccount->id,
            'destination_internal_account_id' => $destinationAccount->id,
            'amount'                          => 100,
        ]);
    }

    /** @test */
    public function assert_events_are_dispatched_as_excepted(): void
    {
        $originAccount = Account::factory()->create([
            'balance' => 1000,
        ]);

        $destinationAccount = Account::factory()->create([
            'balance' => 0,
        ]);

        $data = [
            'type'        => Transaction::TYPES['transfer'],
            'origin'      => $originAccount->id,
            'destination' => $destinationAccount->id,
            'amount'      => 100,
        ];

        Event::fake([
            TransferReceived::class,
            TransferSended::class,
        ]);

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        Event::assertDispatched(TransferReceived::class, function ($event) use ($destinationAccount) {
            return $event->account->id === $destinationAccount->id
                && $event->transaction instanceof Transaction;
        });

        Event::assertDispatched(TransferSended::class, function (TransferSended $event) use ($originAccount) {
            return $event->account->id === $originAccount->id
                && $event->transaction instanceof Transaction;
        });
    }

    /** @test */
    public function assert_transfers_can_not_be_made_from_an_non_existing_account(): void
    {
        $destinationAccount = Account::factory()->create([
            'balance' => 0,
        ]);

        $data = [
            'type'        => Transaction::TYPES['transfer'],
            'origin'      => 9999,
            'destination' => $destinationAccount->id,
            'amount'      => 100,
        ];

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(404);

        $response->assertSee(0);
    }

    /** @test */
    public function assert_the_destination_account_is_created_when_a_transfer_is_made_to_an_non_existing_account(): void
    {
        $originAccount = Account::factory()->create([
            'balance' => 1000,
        ]);

        $data = [
            'type'        => Transaction::TYPES['transfer'],
            'origin'      => $originAccount->id,
            'destination' => 9999,
            'amount'      => 100,
        ];

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $response->assertExactJson([
            'origin' => [
                'id'      => $originAccount->id,
                'balance' => 900,
            ],
            'destination' => [
                'id'      => "9999",
                'balance' => 100,
            ],
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => $originAccount->id,
            'balance' => 900,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => 9999,
            'balance' => 100,
        ]);

        $this->assertDatabaseHas('transactions', [
            'type'                            => Transaction::TYPES['transfer'],
            'origin_internal_account_id'      => $originAccount->id,
            'destination_internal_account_id' => 9999,
            'amount'                          => 100,
        ]);
    }
}
