<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyBranding extends Model
{
    protected $table = 'companyBranding';
    protected $fillable = ['id', 'name_company', 'phone', 'address', 'email', 'logo'];
}
