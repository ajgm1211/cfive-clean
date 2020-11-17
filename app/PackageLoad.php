<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageLoad extends Model
{
    protected $fillable = ['type_cargo','quantity','height','width','large','weight','quote_id'];

    public function quote()
    {
        return $this->belongsTo('App\Quote');
    }
}
