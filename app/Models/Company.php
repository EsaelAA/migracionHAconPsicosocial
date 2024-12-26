<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'admin_companies';

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'company_name',
        'company_nit',
        'city_id',
        'arl_type_id',
        'client_type_id',
        'reinsurer_type_id',
        'contact',
        'position_contact',
        'contact_email',
        'address',
        'phone',
        'cell_phone',
        'policy',
        'logo',
        'color',
        'psychosocial_state'
    ];
}
