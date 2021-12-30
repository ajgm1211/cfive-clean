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

    public function duplicate($company_group)
    {
        $new_model = $this->replicate();

        $new_model->company_group_id = $company_group->id;

        $new_model->push();

        $new_model->save();

        return $new_model;
    }
}
