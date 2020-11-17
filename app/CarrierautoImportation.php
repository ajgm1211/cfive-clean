<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarrierautoImportation extends Model
{
    protected $table    = "carriers_auto_importation";
    protected $fillable = ['id','carrier_id','auto_importation_id'];
    
    public function autoImportation(){
        return $this->belongsTo('App\AutoImportation','auto_importation_id');
    }
    
    public function carrier(){
        return $this->belongsTo('App\Carrier','carrier_id');
    }
}
