<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delegation extends Model
{
    protected $table    = "delegations";
    protected $fillable = ['name', 'address', 'phone','company_user_id'];

    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }
}