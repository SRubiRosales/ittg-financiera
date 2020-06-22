<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loans = DB::table('loans')
            ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')
            ->leftJoin('payments', 'payments.loan_id', '=', 'loans.id')
            ->select(
                'loans.id as loan_id',
                'clients.name as client',
                'loans.amount as cantidad_ministrada',
                'loans.quota as cuota',
                'loans.payments_n as num_de_pagos',
                DB::raw('MAX(payments.payment_number) AS payments'),
                'loans.total as total',
                'loans.due_date as fecha_vencimiento',
                'loans.finished as status',
                DB::raw('SUM(payments.received_amount) AS paid'),
                DB::raw('loans.total - SUM(payments.received_amount) AS rest'))
            ->groupBy('loans.id')->get();
        return response()->json(['loans' => $loans], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($loan_id, $payments, $quota, $paid)
    {
        $date = Carbon::now()->toDateString();
        for($i = 1; $i <= $payments; $i++){
            $due_date = Carbon::parse($date)->addWeeks($i);
            if($paid >= $quota){
                $received = $amount;
            }
            elseif(($paid < $quota) && ($paid > 0)){
                $received = $paid;
            }
            else{
                $received = 0;
            }
            Payment::create([
                'loan_id'  => $loan_id,
                'payment_number' => $i,
                'amount' => $quota,
                'date_payment' => $due_date,
                'received_amount' => $received,
            ]);
            $paid = $paid - $quota;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $payment = Payment::where('loan_id', '=', $id)->get();
        $loan = DB::table('loans')
            ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')
            ->leftJoin('payments', 'payments.loan_id', '=', 'loans.id')
            ->select(
                'clients.name',
                'loans.id as loan_id',
                'payments.payment_number',
                'loans.amount',
                'loans.total',
                'loans.quota',
                'loans.finished',
                DB::raw('SUM(payments.received_amount) AS paid'),
                DB::raw('loans.total-SUM(payments.received_amount) AS rest'))
            ->where('loan_id', '=', $id)->first();
        return response()->json(['payment' => $payment, 'loan' => $loan], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function pagar(Request $request)
    {
        $cantidad = $request->cantidad;//Cantidad que dio el cliente
        $id = $request->loan_id;//Id del préstamo
        $payments = Payment::select('id', 'payment_number', 'amount', 'received_amount')->where('loan_id', '=', $id)->get();
        $paid = $payments->sum('received_amount') + $cantidad;
        foreach($payments as $p) {//Recorre los pagos del préstamo
            if(($p->received_amount < $p->amount) && ($paid >= $p->amount )){
                $payment = Payment::whereId($p->id)
                    ->update([
                    'received_amount' => $p->amount//Actualiza la cantidad recibida
                ]);
            }
            elseif(($p->received_amount < $p->amount) && ($paid < $p->amount) && ($paid > 0)){
                $payment = Payment::whereId($p->id)
                    ->update([
                    'received_amount' => $paid//Actualiza la cantidad recibida
                ]);
            }
            $paid = $paid - $p->amount;//Resta la cuota por pago a la cantidad recibida
        }
        $pagosActualizados = Payment::where('loan_id', '=', $id)->get();
        $loan = DB::table('loans')
            ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')
            ->leftJoin('payments', 'payments.loan_id', '=', 'loans.id')
            ->select(
                'clients.name',
                'loans.id as loan_id',
                'payments.payment_number',
                'loans.amount',
                'loans.total',
                'loans.quota',
                'loans.finished',
                DB::raw('SUM(payments.received_amount) AS paid'),
                DB::raw('loans.total-SUM(payments.received_amount) AS rest'))
            ->where('loan_id', '=', $id)->first();
        return response()->json(['payment' => $pagosActualizados, 'loan' => $loan], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
