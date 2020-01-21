<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Charge extends Model implements Auditable
{
    use OwenIt\Auditing\Auditable;
    
    protected $casts = [
        'amount' => 'array',
        'markups' => 'array',
        'total' => 'array',
    ];

    protected $fillable = ['automatic_rate_id','type_id','surcharge_id','calculation_type_id','amount','markups','currency_id','total'];

    public function automatic_rate()
    {
        return $this->belongsTo('App\AutomaticRate','automatic_rate_id','id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function type()
    {
        return $this->belongsTo('App\Type');
    }

    public function surcharge()
    {
        return $this->hasOne('App\Surcharge','id','surcharge_id');
    }

    public function calculation_type()
    {
        return $this->hasOne('App\CalculationType','id','calculation_type_id');
    }
}
