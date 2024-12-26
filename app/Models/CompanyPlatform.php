<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPlatform extends Model
{
    protected $table = 'admin_company_platform';

    protected $primaryKey = 'company_platform_id';

    protected $fillable = ['platform_id', 'company_id'];
}
