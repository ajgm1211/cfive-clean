<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Failedcontact extends Model
{
    protected $table    = "failed_contacts";
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'position',
        'company_id',
        'company_user_id'
    ];
}
