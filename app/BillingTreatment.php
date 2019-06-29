<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingTreatment extends Model
{
    public function billing() {
        return $this->belongsTo(User::class);
    }


    public function treatment() {
        return $this->belongsTo(Treatment::class);
    }
}
