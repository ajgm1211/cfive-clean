<?php

namespace App;

use App\Http\Filters\LocalChargeLclFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class LocalChargeLcl extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $dates = ['deleted_at'];
    protected $table = 'localcharges_lcl';
    protected $fillable = ['id', 'surcharge_id', 'typedestiny_id', 'contractlcl_id', 'calculationtypelcl_id', 'ammount', 'minimum', 'currency_id', 'created_at', 'updated_at'];

    public function contract()
    {
        return $this->belongsTo('App\ContractLcl', 'contractlcl_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function calculationtypelcl()
    {
        return $this->belongsTo('App\CalculationTypeLcl');
    }

    public function surcharge()
    {
        return $this->belongsTo('App\Surcharge');
    }

    public function localcharportslcl()
    {
        return $this->hasMany('App\LocalCharPortLcl', 'localchargelcl_id');
    }

    public function localcharcountrieslcl()
    {
        return $this->hasMany('App\LocalCharCountryLcl', 'localchargelcl_id');
    }

    public function localcharcarrierslcl()
    {
        return $this->hasMany('App\LocalCharCarrierLcl', 'localchargelcl_id');
    }

    public function typedestiny()
    {
        return $this->belongsTo('App\TypeDestiny');
    }

    /**
     * Scope a query filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request $request;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new LocalChargeLclFilter($request, $builder))->filter();
    }

    /**
     * Scope a query to only include surcharges by contract.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByContract($query, $contract_id)
    {
        return $query->where('contractlcl_id', '=', $contract_id);
    }

    /**
     * Sync LocalCharge Carriers.
     *
     * @param  array  $carrier
     * @return void
     */
    public function LocalChargeCarrierSync($carriers)
    {
        DB::table('localcharcarriers_lcl')->where('localchargelcl_id', '=', $this->id)->delete();

        foreach ($carriers as $carrier_id) {
            LocalCharCarrierLcl::create([
                'carrier_id'    => $carrier_id,
                'localchargelcl_id'   => $this->id,
            ]);
        }
    }

    /**
     * Sync LocalCharge Ports.
     *
     * @param  array $origin_ports
     * @param  array $destination_ports
     * @return void
     */
    public function LocalChargePortsSync($origin_ports, $destination_ports)
    {
        DB::table('localcharports_lcl')->where('localchargelcl_id', '=', $this->id)->delete();

        foreach ($origin_ports as $origin) {
            foreach ($destination_ports as $destination) {
                LocalCharPortLcl::create([
                    'port_orig' => $origin,
                    'port_dest' => $destination,
                    'localchargelcl_id' => $this->id,
                ]);
            }
        }
    }

    /**
     * Sync LocalCharge Countries.
     *
     * @param  array $origin_countries
     * @param  array $destination_countries
     * @return void
     */
    public function LocalChargeCountriesSync($origin_countries, $destination_countries)
    {
        DB::table('localcharcountry_lcl')->where('localchargelcl_id', '=', $this->id)->delete();

        foreach ($origin_countries as $origin) {
            foreach ($destination_countries as $destination) {
                LocalCharCountryLcl::create([
                    'country_orig' => $origin,
                    'country_dest' => $destination,
                    'localchargelcl_id' => $this->id,
                ]);
            }
        }
    }

    /* Duplicate LocalCharge Model instance with relations */
    public function duplicate($contract_id = null)
    {
        $new_localcharge = $this->replicate();

        if ($contract_id) {
            $new_localcharge->contractlcl_id = $contract_id;
        }

        $new_localcharge->save();

        $this->load('localcharcarrierslcl.carrier', 'localcharcountrieslcl.countryOrig', 'localcharcountrieslcl.countryDest', 
        'localcharportslcl.portOrig', 'localcharportslcl.portDest');

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {
                $newRelationship = $relationRecord->replicate();
                $newRelationship->localchargelcl_id = $new_localcharge->id;
                $newRelationship->save();
            }
        }

        return $new_localcharge;
    }
}
