<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    public function patient () {
        return $this->belongsTo(User::class,'user_id');
    }

    public function doctor () {
        return $this->belongsTo(User::class,'doctor_id');
    }
}
