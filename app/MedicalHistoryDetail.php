<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalHistoryDetail extends Model
{
    public function users() {
        return $this->hasMany(User::class);
    }
}
