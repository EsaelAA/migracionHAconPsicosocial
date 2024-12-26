<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionnaires extends Model
{
    protected $table = 'psychosocial_questionnaires';

    protected $primaryKey = 'questionnaire_id';

    protected $fillable = ['measurement_id', 'employee_id', 'type_questionarie', 'consent', 'consent_date', 'data_treatment', 'data_treatment_date', 'state_intrawork', 'state_extrawork', 'state_general_data', 'state_stress', 'state_weather', 'state_crafft', 'state_copping'];
}
