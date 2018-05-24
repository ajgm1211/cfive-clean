<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermsPort extends Model
{
    protected $table = "termsport";
    protected $fillable = ['id', 'port_id', 'term_id'];

    public function term(){
        return $this->belongsTo('App\TermAndCondition', 'term_id');
    }
    public function port(){
        return $this->belongsTo('App\Harbor', 'port_id');
    }
}
