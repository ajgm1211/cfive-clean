<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemarkHarbor extends Model
{
    protected $table = "remark_harbors";
    protected $fillable = ['id', 'port_id', 'remark_condition_id'];

    public function remark()
    {
        return $this->belongsTo('App\RemarkCondition', 'remark_condition_id');
    }
    public function port()
    {
        return $this->belongsTo('App\Harbor', 'port_id');
    }
}