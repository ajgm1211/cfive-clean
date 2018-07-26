<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleTermSurcharge extends Model
{
	protected $fillable =['surcharge_id','sale_term_id'];

	public function sale()
	{
		return $this->belongsTo('App\CompanyUser');
	}

}
