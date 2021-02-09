<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDelegation extends Model
{
    protected $table    = "users_delegations";
    protected $fillable = ['id', 'users_id', 'delegations_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id');
    }
    public function delegation()
    {
        return $this->belongsTo('App\Delagation', 'delegations_id');
    }

}