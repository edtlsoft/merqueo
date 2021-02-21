<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CashRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanWithdrawAllMoneyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_withdraw_all_money_from_the_cash_register()
    {
        // Given
        $moneybase = [
            'D100000' => 0, 'D50000' => 0, 'D20000' => 5, 'D10000' => 10, 'D5000' => 0, 'D1000' => 0, 'D500' => 15, 'D200' => 5, 'D100' => 0, 'D50' => 0,
        ];

        CashRegister::updateAllDenominations($moneybase);
        
        // When
        $response = $this->deleteJson(route('cash-register.destroy'));

        $response->assertStatus(200);

        foreach($moneybase as $denomination => $quantity) {
            $moneybase[$denomination] = 0;
        }

        $response->assertJson([
            'message' => 'La caja registrado se vacio correctamente.',
            'totalWithdraw' => 208500,
            'cashRegister' => $moneybase
        ]);
    }
}
