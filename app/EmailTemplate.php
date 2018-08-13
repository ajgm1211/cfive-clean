<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
	protected $fillable = ['id', 'name', 'subject', 'menssage', 'user_id', 'company'];

	public function user(){
		return $this->belongsTo('App\User');
	}

	public function company_user(){
		return $this->belongsTo('App\CompanyUser');
	}
}
