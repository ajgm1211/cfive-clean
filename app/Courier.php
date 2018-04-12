<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $table    = "couriers";
    protected $fillable = ['id', 'name'];
    public function contract()
    {
         return $this->hasOne('App\Contract');
    }
}
