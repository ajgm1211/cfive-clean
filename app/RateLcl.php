<?php

namespace App;

use App\Http\Filters\OceanFreightLclFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class RateLcl extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'rates_lcl';
    protected $fillable = ['id', 'origin_port', 'destiny_port', 'carrier_id', 'contractlcl_id', 'uom', 'minimum', 'currency_id', 'schedule_type_id', 'transit_time', 'via'];

    public function contract()
    {
        return $this->belongsTo('App\ContractLcl', 'contractlcl_id');
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

    /* Duplicate Rate Model instance */
    public function duplicate()
    {
        $new_rate = $this->replicate();
        $new_rate->save();

        return $new_rate;
    }

    /**
     * Scope a query to only include rates by contract.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByContract($query, $contract_id)
    {
        return $query->where('contractlcl_id', '=', $contract_id);
    }

    /**
     * Scope a query filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Http\Request $request;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new OceanFreightLclFilter($request, $builder))->filter();
    }
}
