<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolfResult extends Model
{
    use HasFactory;

    protected $connection = 'dtools';
    protected $table = 'golf_results';

    public function reservation()
    {
        return $this->belongsTo(GolfReservation::class);
    }
}
