<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleType extends Model
{
    protected $table    = "schedule_type";
    protected $fillable =['id','name'];
}
