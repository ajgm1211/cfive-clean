<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemarkCondition extends Model
{
  protected $table = "termsAndConditions";
  protected $fillable = ['id', 
                         'user_id',
                         'name',
                         'import',
                         'export',
                         'company_user_id',
                         'language_id'
                        ];

  public function user(){
    return $this->belongsTo('App\User');
  }


  public function remarksCarriers(){
    return $this->HasMany('App\RemarkCarrier','remark_condition_id');
  }
    public function remarksHarbors(){
    return $this->HasMany('App\RemarkHarbor','remark_condition_id');
  }

  public function language(){
    return $this->belongsTo('App\Language','language_id');
  }

}
