<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateApi extends Model
{
    protected $table = 'rate_apis';
    protected $fillable = ['id', 'origin_port', 'destiny_port', 'carrier_id', 'contract_id', 'twuenty', 'forty', 'fortyhc', 'fortynor', 'fortyfive', 'currency_id', 'schedule_type_id', 'transit_time', 'via'];

    public function contract()
    {
        return $this->belongsTo('App\ContractApi', 'contract_id');
    }

    public function port_origin()
    {
        return $this->belongsTo('App\Harbor', 'origin_port');
    }

    public function port_destiny()
    {
        return $this->belongsTo('App\Harbor', 'destiny_port');
    }

    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function scheduletype()
    {
        return $this->belongsTo('App\ScheduleType', 'schedule_type_id');
    }

    public function scopeContain($query, $code)
    {
        $valor = 'containers->C'.$code;

        return $query->orwhere($valor, '!=', 0);
    }
}
