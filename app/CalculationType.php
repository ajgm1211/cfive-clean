<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
class CalculationType extends Model
{
    use Rememberable;
    protected $table = "calculationtype";
    protected $fillable = ['id', 'name', 'options', 'gp_pcontainer', 'group_container_id', 'display_name'];

    public function localcharge()
    {
        return $this->hasOne('App\LocalCharge');
    }

    public function containersCalculation()
    {
        return $this->hasMany('App\ContainerCalculation', 'calculationtype_id');
    }
}
