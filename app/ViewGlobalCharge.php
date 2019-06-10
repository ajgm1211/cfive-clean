<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewGlobalCharge extends Model
{
	protected $table = 'views_globalcharges';

	public function contract()
	{
		return $this->belongsTo('App\Contract');
	}

	public function origin_harbor()
	{
		return $this->hasOne('App\Harbor','id','orig_port');
	}

	public function destination_harbor()
	{
		return $this->hasOne('App\Harbor','id','dest_port');
	}
}
