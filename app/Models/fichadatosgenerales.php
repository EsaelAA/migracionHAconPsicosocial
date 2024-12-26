<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class fichadatosgenerales extends Model
{
    protected $table = 'fichadatosgenerales';

    protected $primaryKey = 'cc';

    protected $fillable = [
        'fechaaplicacion',
        'cc',
        'nit',
        'tipodecargo',
    ];
}
