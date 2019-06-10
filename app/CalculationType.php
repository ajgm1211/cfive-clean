<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalculationType extends Model
{


    protected $table    = "calculationtype";
    protected $fillable = ['id', 'name'];


    public function localcharge()
    {

        return $this->hasOne('App\LocalCharge');
    }


}
