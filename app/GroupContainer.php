<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupContainer extends Model
{
    protected $table    = "group_containers";
    protected $fillable = ['id','name'];
	
	public function containers(){
		return $this->hasMany('App\Container','gp_container_id');
	}
}
