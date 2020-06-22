<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Client;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class LoansController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loans = Loan::join('clients', 'loans.client_id', '=', 'clients.id')
                    ->select('loans.*', 'clients.name as client')
                    ->get();
        //$clients = DB::table('clients') -> select ('id','name') -> get();
        return response()->json([
            'loans' => $loans,
            'clients' => Client::pluck('name')->all()
            ],
            200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Calcula fecha de vencimiento a partir del número de pagos (1 pago por semana)
        $client_id = Client::where('name', $request->client)->first();
        $ministering = Carbon::now()->toDateString();
        $payments = $request->payments_n;
        $quota = $request->quota;
        $total = $payments * $quota;
        $due_date = Carbon::parse($ministering)->addWeeks($payments);
        $loan = Loan::create([
            'client_id'  => $client_id->id,
            'payments_n' => $request->payments_n,
            'amount' => $request->amount,
            'quota' => $quota,
            'total' => $total,
            'ministering_date' => $ministering,
            'due_date' => $due_date,
            'finished' => false,
        ]);
        app(PaymentsController::class)->store($loan->id, $payments, $quota, 0);
        return response()->json(['loan' => $loan, 'client' => $request->client], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = Loan::join('clients', 'loans.client_id', '=', 'clients.id')
                    ->select('loans.*', 'clients.name as client')
                    ->where('loans.id', '=', $id)
                    ->first();
        $payment = DB::table('payments')->select('received_amount')->where('loan_id', '=', $loan->id)->sum('received_amount');
        return response()->json(['loan' => $loan, 'payment' => $payment], 200);
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
        $loan = Loan::find($id);
        $client_id = Client::where('name', $request->client)->first();
        
        $payments = $request->payments_n;
        $quota = $request->quota;
        $total = $payments * $quota;
        $ministering = Carbon::now()->toDateString();
        $due_date = Carbon::parse($ministering)->addWeeks($payments);

        $loan->client_id = $client_id->id;
        $loan->amount = $request->amount;
        $loan->payments_n = $payments;
        $loan->quota = $quota;
        $loan->total = $total;
        $loan->ministering_date = $ministering;
        $loan->due_date = $due_date;
        $loan->finished = false;
        $loan->save();
        $pagos = DB::table('payments')->select()->where('loan_id', '=', $loan->id);
        $paid = DB::table('payments')->select('received_amount')->where('loan_id', '=', $loan->id)->sum('received_amount');
        $pagos->delete();
        app(PaymentsController::class)->store($loan->id, $payments, $quota, $paid);
        return response()->json(['loan' => $loan], 200);
    }

    public function status($id)
    {
        Loan::whereId($id)->update(['finished' => 1]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payments = DB::table('payments')->where('loan_id', '=', $id)->delete();
        $loan = Loan::find($id);
        $loan->delete();
        return response()->json(['loan' => $loan], 200);
    }
}
