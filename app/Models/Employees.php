<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $table = 'psychosocial_employees';

    protected $primaryKey = 'employee_id';

    protected $fillable =
    [
        'company_id',
        'measurement_id',
        'city',
        'document_employee',
        'first_name',
        'last_name',
        'position',
        'position_type',
        'email',
        'username',
        'password',
        'first_level',
        'second_level',
        'third_level',
        'fourth_level',
        'fifth_level',
        'sixth_level',
        'seventh_level',
        'eighth_level',
        'type_questionarie',
        'state'
    ];
}
