<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractCompanyRestriction extends Model
{
    protected $fillable = ['company_id','contract_id'];
}
