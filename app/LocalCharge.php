<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Filters\LocalChargeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\LocalCharPort;
use App\LocalCharCarrier;
use App\LocalCharCountry;
use Illuminate\Support\Facades\DB;

class LocalCharge extends Model
{
	use SoftDeletes;
	protected $dates    = ['deleted_at'];
	protected $table    = "localcharges";
	protected $fillable = [ 
		'id',
		'surcharge_id',
		'typedestiny_id',
		'contract_id',
		'calculationtype_id',
		'ammount',
		'currency_id',
		'created_at',
		'updated_at'
	];

	public function contract()
	{
		return $this->belongsTo('App\Contract');
	}

	public function currency(){

		return $this->belongsTo('App\Currency');

	}
	public function calculationtype(){

		return $this->belongsTo('App\CalculationType');

	}
	public function surcharge(){

		return $this->belongsTo('App\Surcharge');

	}
	public function localcharports(){

		return $this->hasMany('App\LocalCharPort','localcharge_id');

	}
	public function localcharcountries(){

		return $this->hasMany('App\LocalCharCountry','localcharge_id');

	}
	public function localcharcarriers(){
		return $this->hasMany('App\LocalCharCarrier','localcharge_id');

	}
	public function typedestiny(){
		return $this->belongsTo('App\TypeDestiny');

	}

	/**
	* Scope a query filter
	*
	* @param  \Illuminate\Database\Eloquent\Builder  $query
	* @param  \Illuminate\Http\Request $request;
	* @return \Illuminate\Database\Eloquent\Builder
	*/
	public function scopeFilter(Builder $builder, Request $request)
	{
		return (new LocalChargeFilter($request, $builder))->filter();
	}

	/**
	* Scope a query to only include surcharges by contract.
	*
	* @param  \Illuminate\Database\Eloquent\Builder $query
	* @return \Illuminate\Database\Eloquent\Builder
	*/
	public function scopeFilterByContract( $query, $contract_id )
	{
		return $query->where( 'contract_id', '=', $contract_id );
	}


	/**
	* Sync LocalCharge Carriers
	*
	* @param  Array  $carrier
	* @return void
	*/
	public function LocalChargeCarrierSync($carriers)
	{
		DB::table('localcharcarriers')->where('localcharge_id', '=', $this->id)->delete(); 

		foreach($carriers as $carrier_id){
			LocalCharCarrier::create([
				'carrier_id'    => $carrier_id,
				'localcharge_id'   => $this->id
			]);
		}
	}

	/**
	* Sync LocalCharge Ports
	*
	* @param  Array $origin_ports
	* @param  Array $destination_ports
	* @return void
	*/
	public function LocalChargePortsSync($origin_ports, $destination_ports)
	{
		DB::table('localcharports')->where('localcharge_id', '=', $this->id)->delete(); 

		foreach($origin_ports as $origin){
			foreach ($destination_ports as $destination) {

				LocalCharPort::create([
					'port_orig' => $origin,
					'port_dest' => $destination,
					'localcharge_id' => $this->id
				]);
			}
		}
	}

	/**
	* Sync LocalCharge Countries
	*
	* @param  Array $origin_countries
	* @param  Array $destination_countries
	* @return void
	*/
	public function LocalChargeCountriesSync($origin_countries, $destination_countries)
	{
		DB::table('localcharcountry')->where('localcharge_id', '=', $this->id)->delete(); 

		foreach($origin_countries as $origin){
			foreach ($destination_countries as $destination) {

				LocalCharCountry::create([
					'country_orig' => $origin,
					'country_dest' => $destination,
					'localcharge_id' => $this->id
				]);
			}
		}
	}
}
