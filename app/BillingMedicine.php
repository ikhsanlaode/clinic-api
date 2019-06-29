<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingMedicine extends Model
{
    public function billing() {
        return $this->belongsTo(User::class);
    }


    public function medicene() {
        return $this->belongsTo(Medicine::class);
    }
}
