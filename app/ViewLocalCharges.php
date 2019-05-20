<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewLocalCharges extends Model
{
	protected $table = 'views_localcharges';

	public function contract()
	{
		return $this->belongsTo('App\Contract');
	}
}
