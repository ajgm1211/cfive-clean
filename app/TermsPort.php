<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermsPort extends Model
{
    protected $table = "termsport";
    protected $fillable = ['id', 'port', 'term_id'];

    public function term(){
        return $this->belongsTo('App\TermAndCondition', 'term_id');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor', 'port');
    }
}
