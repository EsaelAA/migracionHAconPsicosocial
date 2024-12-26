<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stress extends Model
{
    protected $table = 'psychosocial_stress';

    protected $primaryKey = 'stress_id';

    protected $fillable = [
        'questionnaire_id',
        'measurement_id',
        'response_date',
        'position_type',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
        'answer_5',
        'answer_6',
        'answer_7',
        'answer_8',
        'answer_9',
        'answer_10',
        'answer_11',
        'answer_12',
        'answer_13',
        'answer_14',
        'answer_15',
        'answer_16',
        'answer_17',
        'answer_18',
        'answer_19',
        'answer_20',
        'answer_21',
        'answer_22',
        'answer_23',
        'answer_24',
        'answer_25',
        'answer_26',
        'answer_27',
        'answer_28',
        'answer_29',
        'answer_30',
        'answer_31'
    ];
}
