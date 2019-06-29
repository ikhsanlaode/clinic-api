<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    public function doctor() {
        return $this->belongsTo(User::class,'doctor_id');
    }
}
