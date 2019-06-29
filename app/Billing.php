<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    public function patient() {
        return $this->belongsTo(User::class,'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(User::class,'doctor_id');
    }

    public function medicenes() {
        return $this->hasMany(BillingMedicine::class);
    }
}
