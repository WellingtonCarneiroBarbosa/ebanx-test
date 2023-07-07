<?php

namespace Tests\Feature\Transactions;

use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
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
            'balance' => (10 / 100),
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
    public function assert_handleCreateAccountRequest_function_is_not_called_when_already_exists_an_account_with_the_passed_id(): void
    {
        $data = [
            'type'        => 'deposit',
            'destination' => '100',
            'amount'      => 10,
        ];

        $mock = Mockery::mock(TransactionController::class)->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('handleCreateAccountRequest')->never();

        $this->post(route('transaction'), $data);

        $this->app->instance(TransactionController::class, $mock);

        $response = $this->post(route('transaction'), $data);

        $response->assertStatus(201);

        $mock->shouldNotHaveReceived('handleCreateAccountRequest');
    }
}
