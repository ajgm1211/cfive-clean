<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Container extends Model
{
    use Rememberable;
    protected $table = 'containers';
    protected $fillable = ['id', 'name', 'code', 'gp_container_id'];

    public function groupContainer()
    {
        return $this->belongsTo('App\GroupContainer', 'gp_container_id');
    }

    public function containersCalculationType()
    {
        return $this->hasMany('App\ContainerCalculation', 'container_id');
    }
}
