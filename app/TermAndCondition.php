<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermAndCondition extends Model
{
    //

    protected $table = "termsAndConditions";
    protected $fillable = ['id', 'user_id', 'name', 'port', 'import', 'export'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function ports(){
        return $this->belongsTo('App\Harbor', 'port');
    }
}
