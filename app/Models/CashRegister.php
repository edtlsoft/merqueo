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
            $aco[$cash->denomination] = intval($cash->quantity);
            return $aco;
        }, []);
    }
}
