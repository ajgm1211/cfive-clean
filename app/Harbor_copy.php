<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Harbor_copy extends Model
{
    protected $table = 'harbors_copy';
    protected $fillable = ['id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation'];
}
