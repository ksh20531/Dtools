<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolfReservation extends Model
{
    use HasFactory;

    protected $connection = 'dtools';
    protected $table = 'golf_reservations';

    public function field()
    {
        return $this->belongsTo(GolfField::class);
    }
}
