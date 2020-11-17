<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoImportation extends Model
{
    protected $table    = "auto_importations";
    protected $fillable = ['id','name'];
    
    public function carriersAutoImportation(){
        return $this->HasMany('App\CarrierautoImportation');
    }
}
