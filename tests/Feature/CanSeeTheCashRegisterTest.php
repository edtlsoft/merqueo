<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CashRegister;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanSeeTheCashRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_see_the_cash_register()
    {
        $denominations = [
            'D100000' => 1, 'D50000' => 1, 'D20000' => 1, 'D10000' => 1, 'D5000' => 1, 'D1000' => 1, 'D500' => 1, 'D200' => 1, 'D100' => 1, 'D50' => 1,
        ];

        CashRegister::updateAllDenominations($denominations);

        // When
        $response = $this->getJson(route('cash-register.index'));

        $response->assertStatus(200);

        $response->assertJson([
            'cashRegister' => $denominations,
            'totalMoney' => 186850
        ]);
    }
}
