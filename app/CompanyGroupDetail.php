<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyGroupDetail extends Model
{
    protected $table = 'company_group_details';
    protected $fillable = ['company_id', 'company_group_id'];
    public $timestamps = false;

    public function company_group()
    {
        return $this->belongsTo('App\CompanyGroup');
    }

    public function company()
    {
        return $this->belongsTo('App\Company');
    }
}
