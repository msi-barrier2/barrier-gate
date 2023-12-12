<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class RealBarier extends Model
{
    use Compoships;

    public function track()
    {
        return $this->hasMany(TrackStatus::class, ['plant', 'sequence', 'arrival_date'], ['plant', 'sequence', 'arrival_date']);
    }
}
