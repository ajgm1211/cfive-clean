<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subuser extends Model
{
    protected $table    = "subusers";
    protected $fillable = ['id', 'user_id', 'company_id'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
