<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\isNull;

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

        $denominationsDB = self::getAllDenominations();

        foreach($denominations as $denomination => $quantity) {
            $totalDeposited += self::getTheValueOfTheDenomination($denomination) * $quantity;

            $denominationsDB[$denomination] += $quantity;
        }

        self::updateAllDenominations($denominationsDB);

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

            if( \is_null($maxDenomination) ) {
                return null;
            }

            $denomination = "D{$maxDenomination}";

            $denominationsDB[$denomination] -= 1;

            $change -= $maxDenomination;
            
            $denominationsToReturned[$denomination] = isset($denominationsToReturned[$denomination]) 
                ? ($denominationsToReturned[$denomination] + 1) 
                : 1;
        }

        self::updateAllDenominations($denominationsDB);

        return $denominationsToReturned;
    }

    public static function getMaxPosibleDenominationChange($change, $denominations, $execptions=[]) 
    {
        $maxDenomination = self::getMaxDenominationChange($change, $denominations, $execptions);

        if( ! $maxDenomination ) {
            return null;
        }

        $denomination = "D{$maxDenomination}";

        if( $denominations[$denomination] > 0 ) {
            return $maxDenomination;
        }

        $execptions[] = $maxDenomination;

        return self::getMaxPosibleDenominationChange($change, $denominations, $execptions);
    }

    public static function getMaxDenominationChange($change, $denominations, $execptions) 
    {
        $denominations = \array_keys($denominations);

        $denominations = \array_map(['self', 'getTheValueOfTheDenomination'], $denominations);

        \sort($denominations);

        $denominations = \array_filter($denominations, function($denomination) use ($change, $execptions){
            return ($denomination <= $change) && ! \in_array($denomination, $execptions);
        });

        return \array_pop($denominations);
    }
}
