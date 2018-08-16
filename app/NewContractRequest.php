<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewContractRequest extends Model
{
    protected $table = 'newcontractrequests';
    protected $fillable = ['namecontract',
                           'numbercontract',
                           'validation',
                           'company_user_id',
                           'namefile',
                           'user_id',
                           'created',
                           'type',
                           'data'];
}
