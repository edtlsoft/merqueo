<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CanPutMoneyIntoTheCashRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_put_money_into_the_cash_register()
    {
        // Given
        $moneybase = [
            'D100000' => 0,
            'D50000' => 0,
            'D20000' => 5,
            'D10000' => 10,
            'D5000' => 0,
            'D1000' => 0,
            'D500' => 15,
            'D200' => 20,
            'D100' => 0,
            'D50' => 0,
        ];

        // When
        $response = $this->postJson(route('cash-register.store'), $moneybase);

        // Then
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message', 'data'
        ]);

        foreach ($moneybase as $denomination => $quantity) {
            $this->assertDatabaseHas('cash_register', [
                'denomination' => $denomination, 
                'quantity' => $quantity
            ]);
        }
    }

    /** @test */
    public function the_cash_register_only_accept_some_denominations()
    {
        //$this->withoutExceptionHandling();
        // Given
        $moneybase = [
            'D200000' => 0,
            'D100000' => 0,
            'D50000' => 0,
            'D20000' => 5,
            'D15000' => 10,
            'D10000' => 10,
            'D5000' => 0,
            'D1000' => 0,
            'D500' => 15,
            'D300' => 20,
            'D200' => 20,
            'D100' => 0,
            'D50' => 0,
        ];

        // When
        $response = $this->postJson(route('cash-register.store'), $moneybase);

        // Then
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message', 'data'
        ]);

        $this->assertDatabaseMissing('cash_register', ['denomination' => 'D200000']);
        $this->assertDatabaseMissing('cash_register', ['denomination' => 'D15000']);
        $this->assertDatabaseMissing('cash_register', ['denomination' => 'D300']);
    }
}
