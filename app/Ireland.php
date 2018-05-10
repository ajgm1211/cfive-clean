<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ireland extends Model
{


    protected $table    = "irelands";
    protected $fillable =   ['id','provider','type','validity','expire'];

    public function ports(){

        return $this->hasMany('App\IrelandPort');

    }
    public function details(){

        return $this->hasMany('App\IrelandDetails');

    }
}
