<?php

namespace Tests\Feature\Balance;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBalanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_return_the_correct_account_balance(): void
    {
        $account = Account::factory()->create();

        $response = $this->get(route('balance.index', ['account_id' => $account->id]));

        $response->assertStatus(200);

        $response->assertSee($account->balance);

        $account->balance = 1000;
        $account->save();

        $response = $this->get(route('balance.index', ['account_id' => $account->id]));

        $response->assertStatus(200);

        $response->assertSee($account->refresh()->balance);
    }

    /** @test */
    public function it_should_return_not_found_and_0_if_the_account_does_not_exists(): void
    {
        $this->get(route('balance.index', ['account_id' => 999999]))
            ->assertStatus(404)
            ->assertSee(0);
    }
}
