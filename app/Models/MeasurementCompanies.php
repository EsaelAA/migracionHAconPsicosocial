<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementCompanies extends Model
{
    protected $table = 'psychosocial_measurements_companies';

    protected $primaryKey = 'measurements_companie_id';

    protected $fillable = ['measurement_id', 'company_id'];
}
