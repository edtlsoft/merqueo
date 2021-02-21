<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCashRegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $amountPaid = intval($request->amount);

        $denominations = $request->except('amount');

        $totalDeposited = CashRegister::getTotalDeposited($denominations);

        $denominationsToReturn = CashRegister::getDenominationsToReturned($amountPaid, $totalDeposited);

        if( \is_null($denominationsToReturn) ) {
            return response()->json([
                'message' => 'Lo sentimos, en estos momentos no tenemos suficiente efectivo para realizar esta operacion.'
            ], 500);
        }

        Payment::create([
            'amount' => $amountPaid,
            'denominations' => json_encode($denominations),
            'total_deposited' => $totalDeposited,
            'denominations_to_returned' => json_encode($denominationsToReturn)
        ]);

        return response()->json([
            'message' => 'El pago se registro correctamente.',
            'data' => [
                'denominationsToReturned' => $denominationsToReturn,
                'cashRegister' => CashRegister::getAllDenominations()
            ]
        ], 200);
    }
}
