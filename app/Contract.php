<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\ContractFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\ContractCarrier;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection as Collection;

class Contract extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use SearchTraitApi;
    use UtilTrait;
    use \OwenIt\Auditing\Auditable;
    protected $guard = 'web';
    protected $table    = "contracts";

    protected $fillable = ['id', 'name', 'number', 'company_user_id', 'account_id', 'direction_id', 'validity', 'expire', 'status', 'remarks', 'gp_container_id', 'code', 'is_manual', 'result_validator', 'validator'];

    public function rates()
    {
        return $this->hasMany('App\Rate');
    }
    public function addons()
    {
        return $this->hasMany('App\ContractAddons');
    }
    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function localcharges()
    {
        return $this->hasMany('App\LocalCharge');
    }

    public function contract_company_restriction()
    {

        return $this->HasMany('App\ContractCompanyRestriction');
    }

    public function contract_user_restriction()
    {

        return $this->HasMany('App\ContractUserRestriction');
    }

    public function user()
    {

        return $this->belongsTo('App\User');
    }

    public function FilesTmps()
    {
        return $thid->hasMany('App\FileTmp');
    }

    public function carriers()
    {
        return $this->hasMany('App\ContractCarrier', 'contract_id');
    }

    public function direction()
    {
        return $this->belongsTo('App\Direction', 'direction_id');
    }

    public function scopeCarrier($query, $carrier)
    {
        if ($carrier) {
            return $query->where('carrier', $carrier);
        }
        return $query;
    }

    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeDestPort($query, $port_dest)
    {
        if ($port_dest) {
            return $query->where('port_dest', $port_dest);
        }
        return $query;
    }

    public function scopeOrigPort($query, $port_orig)
    {
        if ($port_orig) {
            return $query->where('port_orig', $port_orig);
        }
        return $query;
    }

    /**
     * Return a Group of containers associated to the model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function gpContainer()
    {
        return $this->belongsTo('App\GroupContainer');
    }

    /**
     * Scope a query to only include contracts by authenticated users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id);
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
        return (new ContractFilter($request, $builder))->filter();
    }

    /**
     * Sync Contract Carriers
     *
     * @param  Array  $carrier
     * @return void
     */
    public function ContractCarrierSync($carriers, $api = false)
    {

        DB::table('contracts_carriers')->where('contract_id', '=', $this->id)->delete();

        if ($api) {
            $carriers = explode(",", $carriers);
        }

        foreach ($carriers as $carrier_id) {
            ContractCarrier::create([
                'carrier_id'    => $carrier_id,
                'contract_id'   => $this->id
            ]);
        }
    }

    /**
     * Store file in storage
     *
     * @param  blob  $file
     * @return void
     */
    public function StoreInMedia($file, $name)
    {
        \Storage::disk('FclRequest')->put($name, \File::get($file));
        /*$this->addMedia($file)->addCustomHeaders([
            'ACL' => 'public-read'
        ])->toMediaCollection('document', 'FclRequest');*/
    }

    /**
     * Sync Contract User Restrictions
     *
     * @param  Array $users
     * @return void
     */
    public function ContractUsersRestrictionsSync($users)
    {
        DB::table('contract_user_restrictions')->where('contract_id', '=', $this->id)->delete();

        foreach ($users as $user_id) {
            ContractUserRestriction::create([
                'user_id'    => $user_id,
                'contract_id'   => $this->id
            ]);
        }
    }

    /**
     * Sync Contract Company Restrictions
     *
     * @param  Array $companies
     * @return void
     */
    public function ContractCompaniesRestrictionsSync($companies)
    {
        DB::table('contract_company_restrictions')->where('contract_id', '=', $this->id)->delete();

        foreach ($companies as $company_id) {
            ContractCompanyRestriction::create([
                'company_id'    => $company_id,
                'contract_id'   => $this->id
            ]);
        }
    }

    public function isDry()
    {
        return $this->gpContainer->isDry();
    }

    public function isReefer()
    {
        return $this->gpContainer->isReefer();
    }

    public function isOpenTop()
    {
        return $this->gpContainer->isOpenTop();
    }

    public function isFlatRack()
    {
        return $this->gpContainer->isFlatRack();
    }

    /* Duplicate Contract Model instance with relations */
    public function duplicate()
    {

        $new_contract = $this->replicate();
        $new_contract->name .= ' copy';
        $new_contract->save();

        $this->load('carriers.carrier', 'localcharges', 'rates');
        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {

                if ($relationRecord instanceof \App\LocalCharge)
                    $relationRecord->duplicate($new_contract->id);
                else {
                    $newRelationship = $relationRecord->replicate();
                    $newRelationship->contract_id = $new_contract->id;
                    $newRelationship->save();
                }
            }
        }

        return $new_contract;
    }

    /**
     * processSearchByIdFcl
     *
     * @param  mixed $api_company_id
     * @return void
     */
    public function processSearchByIdFcl($response = false, $convert = false)
    {
        $company_user_id = \Auth::user()->company_user_id;
        $user_id = \Auth::id();
        $container_calculation = ContainerCalculation::get();
        $containers = Container::get();
        $company = CompanyUser::where('id', \Auth::user()->company_user_id)->first();

        /*$chargesOrigin = 'true';
        $chargesDestination = 'true';
        $chargesFreight = 'true';*/
        $markup = null;
        $remarks = "";
        //$remarksGeneral = "";

        $equipment = array();
        $totalesCont = array();

        //Colecciones
        $general = new collection();
        $collectionRate = new Collection();

        //$idCurrency = $company->currency_id;
        $company_user_id = $company->id;

        $equipment = array('1', '2', '3', '4', '5');

        $validateEquipment = $this->validateEquipment($equipment, $containers);

        // Consulta base de datos rates

        if ($validateEquipment['count'] < 2) {
            $rates = Rate::whereHas('contract', function ($q) use ($company_user_id) {
                $q->where('company_user_id', '=', $company_user_id)->where('status', 'publish')->where('name', $this->name);
            })->with(['carrier' => function ($query) {
                $query->select('id', 'name', 'uncode', 'image', 'image as url');
            }])->get();
        }

        //Guard if
        if (count($rates) == 0) {
            return response()->json(['message' => 'No freight rates were found for this trade route', 'state' => 'CONVERSION_PENDING'], 200);
        }

        foreach ($rates as $data) {

            if ($convert) {
                $typeCurrency =  $company->currency->alphacode;
            } else {
                $typeCurrency =  $data->currency->alphacode;
            }

            foreach ($containers as $cont) {
                $totalesContainer = array($cont->code => array('tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0));
                $totalesCont = array_merge($totalesContainer, $totalesCont);
                $var = 'array' . $cont->code;
                $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();
            }


            //$contractStatus = $data->contract->status;
            $collectionRate = new Collection();
            $collectionOrigin = new collection();
            $collectionDestiny = new collection();
            $collectionFreight = new collection();
            $totalRates = 0;
            $totalT = 0;

            //Arreglo totalizador de freight , destination , origin por contenedor
            $totalesCont = array();
            $arregloRateSum = array();

            foreach ($containers as $cont) {
                $totalesContainer = array($cont->code => array('tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0));
                $totalesCont = array_merge($totalesContainer, $totalesCont);
                // Inicializar arreglo rate
                $arregloRate = array('c' . $cont->code => '0');
                $arregloRateSum = array_merge($arregloRateSum, $arregloRate);
            }

            $carrier[] = $data->carrier_id;

            $arregloRate = array();
            //Arreglos para guardar el rate
            $array_ocean_freight = array('type' => 'Ocean Freight', 'detail' => 'Per Container', 'currency' => $data->currency->alphacode);

            $arregloRateSave['markups'] = array();
            $arregloRateSave['rate'] = array();
            //Arreglo para guardar charges
            $arregloCharges['origin'] = array();

            $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);
            // Rates
            $arregloR = $this->ratesSearch($equipment, $markup, $data, $rateC, $typeCurrency, $containers);

            $arregloRateSum = array_merge($arregloRateSum, $arregloR['arregloSaveR']);

            $arregloRateSave['rate'] = array_merge($arregloRateSave['rate'], $arregloR['arregloSaveR']);
            //$arregloRateSave['markups'] = array_merge($arregloRateSave['markups'], $arregloR['arregloSaveM']);
            $arregloRate = array_merge($arregloRate, $arregloR['arregloRate']);

            /*$equipmentFilter = $arregloR['arregloEquipment'];

            $carrier_all = Carrier::where('name', 'ALL')->select('id')->first();

            // ################### Calculos local  Charges #############################

            $localChar = LocalCharge::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'surcharge.saleterm')
                ->with(['currency' => function ($q) {
                    $q->select('id', 'alphacode', 'rates as exchange_usd', 'rates_eur as exchange_eur');
                }])->get();


            foreach ($localChar as $local) {

                $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);

                // Condicion para enviar los terminos de venta o compra
                if (isset($local->surcharge->saleterm->name)) {
                    $terminos = $local->surcharge->saleterm->name;
                } else {
                    $terminos = $local->surcharge->name;
                }

                foreach ($local->localcharcarriers as $localCarrier) {
                    if ($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id == $carrier_all->id) {
                        $localParams = array('terminos' => $terminos, 'local' => $local, 'data' => $data, 'typeCurrency' => $typeCurrency, 'idCurrency' => $idCurrency, 'localCarrier' => $localCarrier);
                        //Origin
                        if ($chargesOrigin != null) {
                            if ($local->typedestiny_id == '1') {
                                foreach ($containers as $cont) {
                                    $name_arreglo = 'array' . $cont->code;
                                    if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                        $collectionOrigin->push($this->processLocalCharge($cont, $local, $localParams, $rateMount, $totalesCont));
                                    }
                                }
                            }
                        }
                        //Destiny
                        if ($chargesDestination != null) {
                            if ($local->typedestiny_id == '2') {
                                foreach ($containers as $cont) {

                                    $name_arreglo = 'array' . $cont->code;

                                    if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                        $collectionDestiny->push($this->processLocalCharge($cont, $local, $localParams, $rateMount, $totalesCont));
                                    }
                                }
                            }
                        }
                        //Freight
                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {
                                $band = false;
                                //Se ajusta el calculo para freight tomando en cuenta el rate currency
                                $rateMount_Freight = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
                                $localParams['typeCurrency'] = $data->currency->alphacode;
                                $localParams['idCurrency'] = $data->currency->id;
                                //Fin Variables

                                foreach ($containers as $cont) {

                                    $name_arreglo = 'array' . $cont->code;

                                    if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                        $collectionFreight->push($this->processLocalCharge($cont, $local, $localParams, $rateMount_Freight, $totalesCont));
                                    }
                                }
                            }
                        }
                    }
                }
            }*/

            $totalRates += $totalT;
            $array = array('type' => 'Ocean Freight', 'detail' => 'Per Container', 'subtotal' => $totalRates, 'total' => $totalRates . " " . $typeCurrency, 'idCurrency' => $data->currency_id, 'currency_rate' => $data->currency->alphacode, 'rate_id' => $data->id);
            $array = array_merge($array, $arregloRate);
            $array = array_merge($array, $arregloRateSave);
            $collectionRate->push($array);

            // SCHEDULE 

            $transit_time = $this->transitTime($data->port_origin->id, $data->port_destiny->id, $data->carrier->id, $data->contract->status);

            $data->setAttribute('via', $transit_time['via']);
            $data->setAttribute('transit_time', $transit_time['transit_time']);
            $data->setAttribute('service', $transit_time['service']);

            // Valores totales por contenedor
            $rateTot = $this->ratesCurrency($data->currency->id, $typeCurrency);

            $sum_origin = 'sum_origin_';
            $sum_freight = 'sum_freight_';
            $sum_destination = 'sum_destination_';

            foreach ($containers as $cont) {
                ${$sum_origin . $cont->code} = 0;
                ${$sum_freight . $cont->code} = 0;
                ${$sum_destination . $cont->code} = 0;
            }

            foreach ($containers as $cont) {
                foreach ($collectionOrigin as $origin) {
                    if ($cont->code == $origin['type']) {
                        $rateCurrency = $this->ratesCurrency($origin['currency_id'], $typeCurrency);
                        ${$sum_origin . $cont->code} +=  $origin['price'] / $rateCurrency;
                    }
                }
                foreach ($collectionFreight as $freight) {
                    if ($cont->code == $freight['type']) {
                        $rateCurrency = $this->ratesCurrency($freight['currency_id'], $typeCurrency);
                        ${$sum_freight . $cont->code} +=  $freight['price'] / $rateCurrency;
                    }
                }
                foreach ($collectionDestiny as $destination) {
                    if ($cont->code == $destination['type']) {
                        $rateCurrency = $this->ratesCurrency($destination['currency_id'], $typeCurrency);
                        ${$sum_destination . $cont->code} +=  $destination['price'] / $rateCurrency;
                    }
                }
            }

            foreach ($containers as $cont) {
                $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] = $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $arregloRateSum['c' . $cont->code];
                $data->setAttribute('tot' . $cont->code . 'F', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_F'], 2, '.', ''));

                $data->setAttribute('tot' . $cont->code . 'O', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_O'], 2, '.', ''));
                $data->setAttribute('tot' . $cont->code . 'D', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_D'], 2, '.', ''));

                $totalesCont[$cont->code]['tot_' . $cont->code . '_F']  = $totalesCont[$cont->code]['tot_' . $cont->code . '_F']  / $rateTot;
                // TOTALES
                $name_tot = 'total' . $cont->code;
                $$name_tot = $totalesCont[$cont->code]['tot_' . $cont->code . '_D'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_O'];
                $$name_tot += ${$sum_origin . $cont->code} + ${$sum_freight . $cont->code} + ${$sum_destination . $cont->code};
                $data->setAttribute($name_tot, number_format($$name_tot, 2, '.', ''));
            }

            //remarks

            if ($data->contract->remarks != "") {
                $remarks = $data->contract->remarks . "<br>";
            }

            //$remarksGeneral .= $this->remarksCondition($data->port_origin, $data->port_destiny, $data->carrier);

            $routes['type'] = 'FCL';
            $routes['origin_port'] = array('name' => $data->port_origin->name, 'code' => $data->port_origin->code);
            $routes['destination_port'] = array('name' => $data->port_destiny->name, 'code' => $data->port_destiny->code);
            $routes['ocean_freight'] = $array_ocean_freight;
            $routes['ocean_freight']['rates'] = $arregloRate;


            if (!empty($collectionFreight)) {
                $routes['freight_charges'] = $collectionFreight;
            }

            if (!empty($collectionDestiny)) {
                $routes['destination_charges'] = $collectionDestiny;
            }

            if (!empty($collectionOrigin)) {
                $routes['origin_charges'] = $collectionOrigin;
            }

            $routes['remarks'] = $remarks;

            $detail = $this->compactResponse($containers, $equipment, $routes, $data, $typeCurrency, $response);

            $general->push($detail);
        }

        return response()->json($general);
    }

    public function compactResponse($containers, $equipment, $routes, $data, $currency, $response)
    {

        switch ($response) {
            case 'compact':
                $detalle = array($data->port_origin->code, $data->port_destiny->code, $data->via);
                foreach ($containers as $cont) {
                    foreach ($equipment as $eq) {
                        if ($eq == $cont->id) {
                            array_push($detalle, (float) $data['total' . $cont->code]);
                        }
                    }
                }
                array_push($detalle, $currency, $data->transit_time ? $data->transit_time : 0, $data->contract->remarks);
                break;
            default:
                $detalle['Rates'] = $routes;

                //Totals
                foreach ($containers as $cont) {
                    foreach ($equipment as $eq) {
                        if ($eq == $cont->id) {
                            $detalle['Rates']['total' . $cont->code] =  $data['total' . $cont->code];
                        }
                    }
                }

                $detalle['Rates']['currency'] = $currency;
                //Schedules
                $detalle['Rates']['schedule']['transit_time'] = $data->transit_time;
                $detalle['Rates']['schedule']['via'] = $data->via;

                //Set carrier
                $detalle['Rates']['carrier'] = $data->carrier;
                //Set contract details
                $detalle['Rates']['contract']['valid_from'] = $data->contract->validity;
                $detalle['Rates']['contract']['valid_until'] =   $data->contract->expire;
                $detalle['Rates']['contract']['number'] =   $data->contract->number;
                $detalle['Rates']['contract']['ref'] =   $data->contract->name;
                $detalle['Rates']['contract']['status'] =   $data->contract->status == 'publish' ? 'published' : $data->contract->status;
                break;
        }

        return $detalle;
    }
}
