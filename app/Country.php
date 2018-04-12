<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table    = "countries";
    protected $fillable = ['id', 'name', 'code'];
    public function contract()
    {
        
        return $this->hasOne('App\Contract');
    }
}
