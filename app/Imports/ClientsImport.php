<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;

class ClientsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Client([
            'id' =>     $row[0],
            'name' =>   $row[1],
            'phone' =>  "55555555",
            'address' => "Su casa",
            //'phone' =>      $row[2],
            //'address' =>    $row[3],
        ]);
    }
}
