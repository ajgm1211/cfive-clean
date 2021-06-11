<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Duplicados extends Model
{
    protected $table = 'duplicate_harbors';
    public $timestamps = false;
    protected $fillable = ['id,id_original,id_duplicado,opciones'];
}
