<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolfField extends Model
{
    use HasFactory;
    protected $connection = 'dtools';
    protected $table = 'golf_fields';
}
