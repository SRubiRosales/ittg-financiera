<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }
}
