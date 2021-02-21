<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\CashRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CashRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_cash_register_can_update_all_denominations()
    {
        $denominations = [
            'D100000' => 10, 'D50000' => 10, 'D20000' => 10, 'D10000' => 10, 'D5000' => 10, 'D1000' => 10, 'D500' => 10, 'D200' => 10, 'D100' => 10, 'D50' => 10,
        ];

        CashRegister::updateAllDenominations($denominations);

        foreach ($denominations as $denomination => $quantity) {
            $denominations[$denomination] = $quantity + 20;
        }

        CashRegister::updateAllDenominations($denominations);

        foreach ($denominations as $denomination => $quantity) {
            $this->assertDatabaseHas('cash_register', [
                'denomination' => $denomination, 
                'quantity' => $quantity
            ]);
        }
    }

    /** @test */
    public function a_cash_register_can_get_all_denominations()
    {
        $denominations = [
            'D100000' => 10, 'D50000' => 10, 'D20000' => 10, 'D10000' => 10, 'D5000' => 10, 'D1000' => 10, 'D500' => 10, 'D200' => 10, 'D100' => 10, 'D50' => 10,
        ];

        CashRegister::updateAllDenominations($denominations);

        $denominationsDB = CashRegister::getAllDenominations();

        $this->assertEquals($denominations, $denominationsDB);
    }

    /** @test */
    public function a_cash_register_can_get_total_deposited()
    {
        $denominations = [
            'D100000' => 1, 'D50000' => 1, 'D20000' => 1, 'D10000' => 1, 'D5000' => 1, 'D1000' => 1, 'D500' => 1, 'D200' => 1, 'D100' => 1, 'D50' => 1,
        ];

        $totalDeposited = CashRegister::getTotalDeposited($denominations);

        $this->assertEquals(186850, $totalDeposited);
    }

    /** @test */
    public function a_cash_register_can_get_the_value_of_the_denomination()
    {
        $this->assertEquals(
            50000,
            CashRegister::getTheValueOfTheDenomination('D50000')
        );
    }

    /** @test */
    public function a_cash_register_can_get_denominations_to_returned()
    {
        // Given
        $denominations = [
            'D100000' => 10, 'D50000' => 10, 'D20000' => 10, 'D10000' => 10, 'D5000' => 10, 'D1000' => 10, 'D500' => 10, 'D200' => 10, 'D100' => 10, 'D50' => 10,
        ];

        CashRegister::updateAllDenominations($denominations);

        $denominationsToReturned = CashRegister::getDenominationsToReturned(10000, 50000);

        $this->assertEquals(
            ['D20000' => 2],
            $denominationsToReturned
        );
    }
}
