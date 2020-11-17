<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserConfiguration extends Model
{
    protected $table = 'users_configurations';
    protected $fillable = ['id','user_id','paramerters'];
}
