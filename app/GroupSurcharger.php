<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSurcharger extends Model
{
    protected $table="group_surchargers";
    protected $fillable=['id','name','varation'];
}
