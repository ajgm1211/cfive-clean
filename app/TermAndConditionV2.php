<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermAndConditionV2 extends Model
{
  //
    protected $table = "termsAndConditionV2s";
    protected $fillable = ['id', 
                           'user_id',
                           'name',
                           'type',
                           'import',
                           'export',
                           'company_user_id',
                           'language_id'
                          ];
  public function language(){
    return $this->belongsTo('App\Language','language_id');
  }
}
