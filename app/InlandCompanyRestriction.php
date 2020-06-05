<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandCompanyRestriction extends Model
{
      	protected $table    = "inlands_company_restrictions";
      	protected $fillable = [ 'company_id', 'inland_id' ];

	    public function company(){
	      	return $this->belongsTo('App\Company', 'company_id');
	    }
}
