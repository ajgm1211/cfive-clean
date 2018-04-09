<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPivot extends Model
{
    protected $table    = "users_pivot";
    protected $fillable = ['id', 'user_id', 'company_id'];
    public function user()
    {
        return $this->hasOne('App\User');
    }
}
