<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractUserRestriction extends Model
{
    protected $fillable = ['user_id','contract_id'];
}
