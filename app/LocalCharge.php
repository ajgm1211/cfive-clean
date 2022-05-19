<?php

namespace App;

use App\Http\Filters\LocalChargeFilter;
use App\LocalCharCarrier;
use App\LocalCharCountry;
use App\LocalCharPort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class LocalCharge extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $dates = ['deleted_at'];
    protected $table = 'localcharges';
    protected $fillable = [
        'id',
        'surcharge_id',
        'typedestiny_id',
        'contract_id',
        'calculationtype_id',
        'ammount',
        'currency_id',
        'created_at',
        'updated_at',
    ];

    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function calculationtype()
    {
        return $this->belongsTo('App\CalculationType');
    }

    public function surcharge()
    {
        return $this->belongsTo('App\Surcharge');
    }

    public function localcharports()
    {
        return $this->hasMany('App\LocalCharPort', 'localcharge_id');
    }

    public function localcharcountries()
    {
        return $this->hasMany('App\LocalCharCountry', 'localcharge_id');
    }

    public function localcharcarriers()
    {
        return $this->hasMany('App\LocalCharCarrier', 'localcharge_id');
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
        return (new LocalChargeFilter($request, $builder))->filter();
    }

    /**
     * Scope a query to only include surcharges by contract.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByContract($query, $contract_id)
    {
        return $query->where('contract_id', '=', $contract_id);
    }

    /**
     * Sync LocalCharge Carriers.
     *
     * @param  array  $carrier
     * @return void
     */
    public function LocalChargeCarrierSync($carriers)
    {
        DB::table('localcharcarriers')->where('localcharge_id', '=', $this->id)->delete();

        foreach ($carriers as $carrier_id) {
            LocalCharCarrier::create([
                'carrier_id'    => $carrier_id,
                'localcharge_id'   => $this->id,
            ]);
        }
    }


    public function getLocalChargeExcelSync($contract_id,$port_origin,$port_destiny,$orig_country,$dest_country)
    {
       $result = DB::select(DB::raw('call proc_getLocalChargeExcel(' . $contract_id . ',"' . $port_origin . '","' . $port_destiny . '","' . $orig_country . '","' . $dest_country . '")'));
        return $result;

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
        DB::table('localcharports')->where('localcharge_id', '=', $this->id)->delete();

        foreach ($origin_ports as $origin) {
            foreach ($destination_ports as $destination) {
                LocalCharPort::create([
                    'port_orig' => $origin,
                    'port_dest' => $destination,
                    'localcharge_id' => $this->id,
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
        DB::table('localcharcountry')->where('localcharge_id', '=', $this->id)->delete();

        foreach ($origin_countries as $origin) {
            foreach ($destination_countries as $destination) {
                LocalCharCountry::create([
                    'country_orig' => $origin,
                    'country_dest' => $destination,
                    'localcharge_id' => $this->id,
                ]);
            }
        }
    }

    /* Duplicate LocalCharge Model instance with relations */
    public function duplicate($contract_id = null)
    {
        $new_localcharge = $this->replicate();

        if ($contract_id) {
            $new_localcharge->contract_id = $contract_id;
        }

        $new_localcharge->save();

        $this->load('localcharcarriers.carrier', 'localcharcountries.countryOrig', 'localcharcountries.countryDest', 'localcharports.portOrig', 'localcharports.portDest');

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {
                $newRelationship = $relationRecord->replicate();
                $newRelationship->localcharge_id = $new_localcharge->id;
                $newRelationship->save();
            }
        }

        return $new_localcharge;
    }
}
