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
}
