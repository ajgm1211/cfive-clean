<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeDestiny extends Model
{
    protected $table    = "typedestiny";
    protected $fillable =   ['id','description'];
    public $timestamps = false;

    public function globalcharport()
    {
        return $this->hasMany('App\GlobalCharPort');
    }

}
