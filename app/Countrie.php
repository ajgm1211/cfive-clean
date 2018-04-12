<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Countrie extends Model
{
    protected $table    = "countries";
    protected $fillable = ['id', 'name', 'code'];
    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }
}
