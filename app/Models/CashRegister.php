<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $table = 'cash_register';

    protected $fillable = ['denomination', 'quantity'];

    public static function updateAllDenominations($denominations)
    {
        self::truncate();

        $data = [];

        foreach ($denominations as $denomination => $quantity) {
            $data[] = ['denomination' => $denomination, 'quantity' => $quantity];
        }

        self::insert($data);

        return $data;
    }

    public static function getAllDenominations()
    {
        return self::all()->reduce(function($aco, $cash){
            $aco[$cash->denomination] = \intval($cash->quantity);
            return $aco;
        }, []);
    }

    public static function getTotalDeposited($denominations)
    {
        $totalDeposited = 0;

        foreach($denominations as $denomination => $quantity) {
            $totalDeposited += self::getTheValueOfTheDenomination($denomination) * $quantity;
        }

        return $totalDeposited;
    }

    public static function getTheValueOfTheDenomination($denomination)
    {
        return \intval(str_replace('D', '', $denomination));
    }

    public static function getDenominationsToReturned($amountPaid, $totalDeposited)
    {
        $denominationsToReturned = [];

        $change = $totalDeposited - $amountPaid;

        $denominationsDB = self::getAllDenominations();

        while($change > 0) {
            $maxDenomination = self::getMaxPosibleDenominationChange($change, $denominationsDB);

            $denomination = "D{$maxDenomination}";

            $denominationsDB[$denomination] -= 1;

            $change -= $maxDenomination;
            
            $denominationsToReturned[$denomination] = isset($denominationsToReturned[$denomination]) 
                ? ($denominationsToReturned[$denomination] + 1) 
                : 1;
        }

        return $denominationsToReturned;
    }

    public static function getMaxPosibleDenominationChange($change, $denominations) 
    {
        $denominations = \array_keys($denominations);

        $denominations = \array_map(['self', 'getTheValueOfTheDenomination'], $denominations);

        \sort($denominations);

        $denominations = \array_filter($denominations, function($denomination) use ($change){
            return ($denomination <= $change);
        });

        return \array_pop($denominations);
    }
}
