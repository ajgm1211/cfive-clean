<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Contact extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['first_name','last_name','phone','email','position','company_id','options'];

    public function company()
    {
        return $this->belongsTo('App\Company','company_id');
    }

    public function scopeCompany($query){
        $query->with(['company' => function ($q) {
            $q->select('id', 'business_name', 'phone', 'address', 'tax_number', 'logo as url');
        }]);
    }

    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }
}
