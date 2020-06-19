<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use DB;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$payments = Payment::all();
        return view('payments.index', [
            'payments' => $payments,
        ]);*/
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($loan_id)
    {
        $p = DB::table('loans')->join('clients', 'loans.client_id', '=', 'clients.id')
                    ->select('loans.id as prestamo', 'clients.name as cliente')
                    ->where('loans.id', '=', $loan_id)
                    ->first();
        return view('payments.create', compact('p'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id'  => 'required',
            //'amount' => 'required',
            //'payment_number' => 'required',
            'date_payment' => 'required',
            'received_amount' => 'required'
        ]);
        //Payment::create($request->all());
        $loan_id = $request->input('loan_id');//Id del préstamo
        $received = $request->input('received_amount');//Cantidad recibida
        $payments = Payment::select('received_amount')->where('loan_id', '=', $loan_id)->get();//Información los pagos que ha hecho
        $payment_number = $payments->count() + 1;//Número de pago
        $loan = DB::table('loans')->select('quota', 'total')->where('id', '=', $loan_id)->first();//Consulta la cuota de ese préstamo y el total a pagar
        $amount = $loan->quota;//Cantidad que correspondía a ese pago
        $paid = $payments->sum('received_amount') + $received;
        
        Payment::create([
            'loan_id'  => $loan_id,
            'payment_number' => $payment_number,
            //'amount' => $request->input('amount'),
            'amount' => $amount,
            'date_payment' => $request->input('date_payment'),
            'received_amount' => $received,
        ]);
        
        if($paid == $loan->total){//Si este es su último pago, actualiza el estado del préstamo
            return redirect()->route('loans.status', $loan_id);
        }
        else{
            return redirect()->route('loans.show', $loan_id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
