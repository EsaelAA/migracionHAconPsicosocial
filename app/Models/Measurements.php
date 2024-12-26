<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measurements extends Model
{
    protected $table = 'psychosocial_measurements';

    protected $primaryKey = 'measurement_id';

    protected $fillable = ['measurement_name', 'measurement_year', 'state', 'start_date', 'end_date'];
}
