<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlertDuplicateGcFcl extends Model
{
    protected $table    = "alerts_duplicates_gc_fcl";
    protected $fillable = ['id',
                           'date',
                           'n_duplicate',
                           'n_company',
                           'status_alert_id'
                          ];

    public function status(){
        return $this->belongsTo('App\StatusAlert','status_alert_id');
    }
}
