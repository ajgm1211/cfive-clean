<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermAndCondition extends Model
{
    //

    protected $table = "termsAndConditions";
    protected $fillable = ['id', 'user_id', 'name', 'import', 'export', 'company_user_id'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function harbor(){
        return $this->HasManyThrough('App\Harbor','App\TermsPort','term_id','id','id','port_id');
    }
    /*public function ports(){
        return $this->belongsTo('App\Harbor', 'port');
    }*/
}
