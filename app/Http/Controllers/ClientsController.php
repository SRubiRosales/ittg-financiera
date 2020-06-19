<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Imports\ClientsImport;
use DB;

class ClientsController extends Controller
{
    public function index()
    {
        return response()->json(['clients' => Client::all()], 200);
    }

    public function store(Request $request)
    {
        $client = Client::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return response()->json(['client' => $client], 200);
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);
        $client->name = $request->name;
        $client->phone = $request->phone;
        $client->address = $request->address;
        $client->save();
        return response()->json(['client' => $client], 200);
    }

    public function exportExcel()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }

    public function importExcel(Request $request)
    {
        //$file = $request->file('file');
        Excel::import(new ClientsImport, $request->file('file'));
        return back()->with('message', 'Clientes importados');
    }

    public function destroy($id)
    {
        $loans = DB::table('loans')->where('client_id', '=', $id)->delete();
        $client = Client::find($id);
        $client->delete();
        return response()->json(['client' => $client], 200);
    }
}
