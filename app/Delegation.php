<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delegation extends Model
{
    protected $table    = "delegations";
    protected $fillable = ['id','name', 'address', 'phone','company_user_id'];

    public function Users()
    {
        return $this->belongsToMany('App\Users');
    }
}