<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ContractUserRestriction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
  
    protected $fillable = ['user_id','contract_id'];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
