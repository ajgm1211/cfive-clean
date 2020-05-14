<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalDuplicated extends Model
{
    protected $table    = "global_duplicateds";
    protected $fillable = ['id',
                           'global_id',
                           'global_dp_id',
                           'gp_global_dp_id'
                          ];
    
    



}
