<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    public function orderDetail()
    {
        return $this->belongsTo('App\OrderDetail');
    }
}
