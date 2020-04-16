<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupContainer extends Model
{
    protected $table    = "group_containers";
    protected $fillable = ['id', 'name', 'code'];
	
	public function containers(){
		return $this->hasMany('App\Container','gp_container_id');
	}

	public function isDry(){
		return $this->code == 'dry';
	}

	public function isReefer(){
		return $this->code == 'reefer';
	}

	public function isOpenTop(){
		return $this->code == 'opentop';
	}

	public function isFlatRack(){
		return $this->code == 'flatrack';
	}
}
