<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BehaviourPerContainer extends Model
{
    protected $table = "behaviour_per_containers";
    protected $fillable = ['id', 'name', 'code'];
}