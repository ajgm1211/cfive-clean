<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalculationType extends Model
{

    protected $table = "calculationtype";
    protected $fillable = ['id', 'name', 'options', 'gp_pcontainer', 'group_container_id', 'display_name','behaviour_pc_id'];

    public function localcharge()
    {
        return $this->hasOne('App\LocalCharge');
    }

    public function containersCalculation()
    {
        return $this->hasMany('App\ContainerCalculation', 'calculationtype_id');
    }
    public function behaviour_per_container()
    {
        return $this->belongsTo('App\BehaviourPerContainer', 'behaviour_pc_id');
    }
}
