<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Filters\OceanFreightFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Rate extends Model
{
    use SoftDeletes;
    protected $dates    = ['deleted_at'];
    
    protected $table    = "rates";
    protected $fillable = ['id', 'origin_port','destiny_port','carrier_id','contract_id','twuenty','forty','fortyhc','fortynor','fortyfive', 'containers','currency_id','schedule_type_id','transit_time','via'];
    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }
    public function port_origin(){
        return $this->belongsTo('App\Harbor','origin_port');
    }
    public function port_destiny(){
        return $this->belongsTo('App\Harbor','destiny_port');
    }
    public function carrier(){
        return $this->belongsTo('App\Carrier');
    }
    public function currency(){
        return $this->belongsTo('App\Currency');
    }
    
    public function scheduletype(){
        return $this->belongsTo('App\ScheduleType','schedule_type_id');
    }

    /**
    * Scope a query filter
    *
    * @param  \Illuminate\Database\Eloquent\Builder $query
    * @param  \Illuminate\Http\Request $request;
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new OceanFreightFilter($request, $builder))->filter();
    }

    public function duplicate(){
        
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
    public function scopeFilterByContract( $query, $contract_id )
    {
        return $query->where( 'contract_id', '=', $contract_id );
    }

protected $casts = [
        'containers' => 'array'
    ];
}
