<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CanMakeAPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_a_payment()
    {
        // Given
        CashRegister::updateAllDenominations([
            'D100000' => 100,
            'D50000' => 100,
            'D20000' => 100,
            'D10000' => 100,
            'D5000' => 100,
            'D1000' => 100,
            'D500' => 100,
            'D200' => 100,
            'D100' => 100,
            'D50' => 100
        ]);
        
        $payment = [
            'amount' => 10000,
            'D100000' => 0,
            'D50000' => 1,
            'D20000' => 0,
            'D10000' => 0,
            'D5000' => 0,
            'D1000' => 0,
            'D500' => 0,
            'D200' => 0,
            'D100' => 0,
            'D50' => 0,
        ];

        // When
        $response = $this->postJson(route('payments.store'), $payment);

        // Then
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'denominationsToReturned' => ['D20000' => 2]
            ]
        ]);

        array_shift($payment);

        $denominationsJson = json_encode($payment);

        $this->assertDatabaseHas('payments', [
            'amount' => 10000,
            'denominations' => $denominationsJson,
            'total_deposited' => 50000
        ]);

        $this->assertDatabaseHas('cash_register', ['denomination' => 'D50000', 'quantity' => 101]);
        $this->assertDatabaseHas('cash_register', ['denomination' => 'D20000', 'quantity' => 98]);
    }

    /** @test */
    public function can_not_make_a_payment_because_the_cash_register_does_not_have_enough_change()
    {
        $this->withoutExceptionHandling();
        // Given
        CashRegister::updateAllDenominations([
            'D100000' => 10,
            'D50000' => 10,
            'D20000' => 0,
            'D10000' => 1,
            'D5000' => 0,
            'D1000' => 1,
            'D500' => 0,
            'D200' => 0,
            'D100' => 0,
            'D50' => 0
        ]);
        
        $payment = [
            'amount' => 20000,
            'D100000' => 1,
            'D50000' => 0,
            'D20000' => 0,
            'D10000' => 0,
            'D5000' => 0,
            'D1000' => 0,
            'D500' => 0,
            'D200' => 0,
            'D100' => 0,
            'D50' => 0,
        ];

        // When
        $response = $this->postJson(route('payments.store'), $payment);

        // Then
        $response->assertStatus(500);

        $response->assertJson([
            'message' => "Lo sentimos, en estos momentos no tenemos suficiente efectivo para realizar esta operacion."
        ]);
    }
}
