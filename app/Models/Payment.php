<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo('App\Models\Loan');
    }
}
