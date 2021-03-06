<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCashRegisterRequest;

class CashRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cashRegister = CashRegister::getAllDenominations();

        $totalMoney = CashRegister::getTotalDeposited($cashRegister);

        return response()->json([
            'cashRegister' => $cashRegister,
            'totalMoney' => $totalMoney
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCashRegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCashRegisterRequest $request)
    {
        $data = CashRegister::updateAllDenominations($request->validated());

        return response()->json([
            'message' => 'El dinero base se registro correctamente.',
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $cashRegister = CashRegister::getAllDenominations();
        $totalWithdraw = CashRegister::getTotalDeposited($cashRegister);

        foreach($cashRegister as $denomination => $quantity) {
            $cashRegister[$denomination] = 0;
        }

        CashRegister::updateAllDenominations($cashRegister);

        return response()->json([
            'message' => 'La caja registrado se vacio correctamente.',
            'totalWithdraw' => $totalWithdraw,
            'cashRegister' => CashRegister::getAllDenominations()
        ], 200);
    }
}
