<?php

namespace App;

use App\ContractCarrier;
use App\Rate;
use App\ContractCompanyRestriction;
use App\ContractUserRestriction;
use App\Http\Filters\ContractFilter;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Contract extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use SearchTraitApi;
    use UtilTrait;
    use \OwenIt\Auditing\Auditable;
    protected $guard = 'web';
    protected $table = "contracts";

    protected $fillable = ['id', 'name', 'number', 'company_user_id', 'account_id', 'direction_id', 'validity', 'expire', 'status', 'remarks', 'gp_container_id', 'code', 'is_manual', 'result_validator', 'validator', 'is_api'];

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

    public function contract_request()
    {

        return $this->hasOne('App\NewContractRequest','contract_id','id');
    }

    public function FilesTmps()
    {
        return $this->hasMany('App\FileTmp');
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
        $status_erased=1;
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id)->where('status_erased','!=',$status_erased);
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
                'carrier_id' => $carrier_id,
                'contract_id' => $this->id,
            ]);
        }
    }

    /**
     * Sync Contract Carriers Single
     *
     * @param  Array  $carrier
     * @return void
     */
    public function ContractCarrierSyncSingle($carrier_id)
    {

        DB::table('contracts_carriers')->where('contract_id', '=', $this->id)->delete();

        ContractCarrier::create([
            'carrier_id' => $carrier_id,
            'contract_id' => $this->id,
        ]);
    }

    public function ContractRateStore($request,$contract,$req,$container){

        $originPort = $request->origin; 
        $destinationPort= $request->destination;

        foreach ($originPort as $origin) {
            foreach($destinationPort as $destination){
                $rates = new Rate();
                $rates->origin_port = $origin['id'];
                $rates->destiny_port = $destination['id'];
                $arreglo = array();
                if ($req == 1) {
                    
                    if(isset($request->rates['C20DV'])){
                        $rates->twuenty = $request->rates['C20DV'];
                    }else{
                        $rates->twuenty = 0;
                    }
                    if(isset($request->rates['C40DV'])){
                        $rates->forty = $request->rates['C40DV'];
                    }else{
                        $rates->forty = 0;
                    }
                    if(isset($request->rates['C40HC'])){
                        $rates->fortyhc = $request->rates['C40HC'];
                    }else{
                        $rates->fortyhc = 0;
                    }
                    if(isset($request->rates['C40NOR'])){
                        $rates->fortynor = $request->rates['C40NOR'];
                    }else{
                        $rates->fortynor = 0;
                    }
                    if(isset($request->rates['C45HC'])){
                        $rates->fortyfive = $request->rates['C45HC'];
                    }else{
                        $rates->fortyfive = 0;
                    }
                } 
                else {

                    $rates->twuenty = 0;
                    $rates->forty = 0;
                    $rates->fortyhc = 0;
                    $rates->fortynor = 0;
                    $rates->fortyfive = 0;

                    foreach ($container as $cod) {
                        $cont = 'C' . $cod->code;
                        if ($cod->gp_container_id == $req) {
                            if(isset($request->rates[$cont])){
                                $arreglo[$cont] = $request->rates[$cont];
                            }else{
                                $arreglo[$cont] = 0;
                            }    
                        }
                    }
                    // dd($arreglo);
                    $rates->containers = json_encode($arreglo);
                }
                $rates->carrier_id = $request->carrier['id'];
                $rates->currency_id = $request->currency['id'];
                $rates->contract()->associate($contract);
                $rates->save();
            }
        }        
    }
    
    public function ContractSurchargeStore($request,$contract){

        $calculation_type = $request->dataSurcharger;
        $originPort = $request->origin; 
        $destinationPort= $request->destination;
        // $typeC = $request->input('type');
        // $currencyC = $request->input('currency');
        // $amountC = $request->input('amount');
        if (count((array)$calculation_type) > 0) {
            foreach ($calculation_type as $ct) {
                if (!empty($request->dataSurcharger['0']['amount'])) {
                    $localcharge = new LocalCharge();
                    $localcharge->surcharge_id = $ct['type']['id'];
                    $localcharge->typedestiny_id = '3';
                    $localcharge->calculationtype_id = $ct['calculation']['id'];
                    $localcharge->ammount = $ct['amount'];
                    $localcharge->currency_id = $ct['currency']['id'];
                    $localcharge->contract()->associate($contract);
                    $localcharge->save();

                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id = $request->carrier['id']; //$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();

                    foreach ($originPort as $origin) {
                        foreach($destinationPort as $destination){
                            $detailport = new LocalCharPort();
                            $detailport->port_orig = $origin['id']; // $request->input('port_origlocal'.$contador.'.'.$orig);
                            $detailport->port_dest = $destination['id']; //$request->input('port_destlocal'.$contador.'.'.$dest);
                            $detailport->localcharge()->associate($localcharge);
                            $detailport->save();
                        }
                    }                    
                }
            }
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
                'user_id' => $user_id,
                'contract_id' => $this->id,
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
                'company_id' => $company_id,
                'contract_id' => $this->id,
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

                if ($relationRecord instanceof \App\LocalCharge) {
                    $relationRecord->duplicate($new_contract->id);
                } else {
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
    public function processSearchByIdFcl()
    {
        $rates = ContractRateFclApi::where('contract_id', $this->id)->get();
        if (count($rates) == 0) {
            return response()->json(['message' => 'The requested contract is pending processing', 'state' => 'CONVERSION_PENDING'], 200);
        }
        $rates = $this->transformToArray($rates);
        return $rates;
    }

    public function transformToArray($rates)
    {
        $equipment = array('1', '2', '3', '4', '5');
        $containers = Container::all();
        $arr = array();
        foreach ($rates as $data) {
            $detalle = array($data->origin_port, $data->destiny_port, $data->via);
            foreach ($containers as $cont) {
                foreach ($equipment as $eq) {
                    if ($eq == $cont->id) {
                        array_push($detalle, (float) $data[$cont->code]);
                    }
                }
            }
            array_push($detalle, $data->currency, $data->transit_time, $data->remarks);
            array_push($arr, $detalle);
        }

        return $arr;
    }
}
