<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanPutMoneyIntoTheCashRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_put_money_into_cash_register()
    {
        $this->withoutExceptionHandling();
        // Given
        $moneybase = [
            '100000' => 0,
            '50000' => 0,
            '20000' => 5,
            '10000' => 10,
            '5000' => 0,
            '1000' => 0,
            '500' => 15,
            '200' => 20,
            '100' => 0,
            '50' => 0,
        ];

        // When
        $response = $this->postJson(route('cash-register.store'), $moneybase);

        // Then
        $response->assertStatus(200);

        foreach ($moneybase as $denomination => $quantity) {
            $this->assertDatabaseHas('cash_register', [
                'denomination' => $denomination, 
                'quantity' => $quantity
            ]);
        }
    }
}
