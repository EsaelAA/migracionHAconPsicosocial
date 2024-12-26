<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralData extends Model
{
    protected $table = 'psychosocial_general_data';

    protected $primaryKey = 'general_data_id';

    protected $fillable = [
        'questionnaire_id',
        'response_date',
        'type_questionarie',
        'gender',
        'birth_date',
        'user_years',
        'civil_status',
        'level_study',
        'occupation',
        'municipality',
        'stratum',
        'type_housing',
        'dependents',
        'municipality_work',
        'years_work',
        'range_years_work',
        'position',
        'position_type',
        'position_years',
        'range_position_years',
        'area',
        'type_contract',
        'hours_work',
        'salary_type'
    ];
}
