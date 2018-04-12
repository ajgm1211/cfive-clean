<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table    = "contracts";

    protected $fillable = ['id', 'name', 'number','validity','expire','status'];
    public function courier(){

        return $this->hasOne('App\Courier');

    }
    public function countrie(){

        return $this->hasOne('App\Countrie');

    }
}
