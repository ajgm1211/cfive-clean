<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $table    = "containers";
    protected $fillable = ['id','name','code','gp_container_id'];
    
    public function groupContainer(){
        return $this->belongsTo('App\GroupContainer','gp_container_id');
    }
    
    public function containersCalculationType(){
        return $this->hasMany('App\ContainerCalculation','container_id');
    }
    
    public function getQuoteContainers($equip){
        $full_equip = [];
        $cont = $this;
        
        foreach ($equip as $eq){
            array_push($full_equip,$cont->where('id','=',$eq)->first()->code);
        };

        return implode(',',$full_equip);
    }
}
