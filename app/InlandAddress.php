<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandAddress extends Model
{
    
    protected $fillable = ['id','quote_id','port_id','inland_address_id','address'];

}
