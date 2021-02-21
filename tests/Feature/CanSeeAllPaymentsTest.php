<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CashRegister;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanSeeAllPaymentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_see_all_payments()
    {
        $denominations = [
            'D100000' => 10, 'D50000' => 10, 'D20000' => 10, 'D10000' => 10, 'D5000' => 10, 'D1000' => 10, 'D500' => 10, 'D200' => 10, 'D100' => 10, 'D50' => 10,
        ];

        CashRegister::updateAllDenominations($denominations);

        $payment = [
            'amount' => 10000, 'D100000' => 0, 'D50000' => 1, 'D20000' => 0, 'D10000' => 0, 'D5000' => 0, 'D1000' => 0, 'D500' => 0, 'D200' => 0, 'D100' => 0, 'D50' => 0,
        ];

        $this->postJson(route('payments.store'), $payment);

        // When
        $response = $this->getJson(route('payments.index'));

        $response->assertStatus(200);

        $response->assertJson([
            'payments' => [
                [
                    'amount' => '10000',
                    'total_deposited' => '50000',
                    'denominations_to_returned' => '{"D20000":2}',
                ]
            ]
        ]);
    }
}
