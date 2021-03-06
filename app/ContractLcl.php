<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use App\CompanyUser;
use App\ScheduleType;
use App\Currency;
use App\RateLcl;
use App\LocalChargeLcl;
use App\GlobalChargeLcl;
use App\ContractRateLclApi;
use App\Http\Filters\ContractLclFilter;
use App\User;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractLcl extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use SoftDeletes;
    use SearchTraitApi;
    use UtilTrait;
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $table    = "contracts_lcl";
    protected $fillable = ['id', 'name', 'number', 'company_user_id', 'user_id', 'direction_id', 'account_id', 'validity', 'expire', 'status', 'code', 'is_manual', 'is_api', 'comments'];

    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function rates()
    {
        return $this->hasMany('App\RateLcl', 'contractlcl_id');
    }

    public function localcharges()
    {
        //return $this->hasManyThrough('App\LocalCharCarrier', 'App\LocalCharge');
        return $this->hasMany('App\LocalChargeLcl', 'contractlcl_id');
    }

    public function contract_company_restriction()
    {

        return $this->HasMany('App\ContractLclCompanyRestriction', 'contractlcl_id');
    }

    public function contract_user_restriction()
    {

        return $this->HasMany('App\ContractLclUserRestriction', 'contractlcl_id');
    }

    public function company_restriction()
    {

        return $this->hasManyThrough('App\Company', 'App\ContractLclCompanyRestriction', 'contractlcl_id', 'id', 'id', 'company_id');
    }

    public function user_restriction()
    {

        return $this->hasManyThrough('App\User', 'App\ContractLclUserRestriction', 'contractlcl_id', 'id', 'id', 'user_id');
    }

    public function carriers()
    {
        return $this->hasMany('App\ContractCarrierLcl', 'contract_id');
    }

    public function direction()
    {
        return $this->belongsTo('App\Direction', 'direction_id');
    }

    /**
     * ContractCarrierSync
     *
     * @param  mixed $carriers
     * @param  mixed $api
     * @return void
     */
    public function ContractCarrierSync($carriers, $api = false)
    {

        DB::table('contracts_carriers_lcl')->where('contract_id', '=', $this->id)->delete();

        if ($api) {
            $carriers = explode(",", $carriers);
        }

        foreach ($carriers as $carrier) {
            ContractCarrierLcl::create([
                'carrier_id'    => $carrier,
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
        \Storage::disk('LclRequest')->put($name, \File::get($file));
        /*$this->addMedia($file)->addCustomHeaders([
            'ACL' => 'public-read'
        ])->toMediaCollection('document', 'LclRequest');*/
    }

    public function unidadesTON($unidades)
    {

        if ($unidades < 1) {
            return 1;
        } else {
            return $unidades;
        }
    }
    /**
     * processSearchByIdLcl
     *
     * @return json
     */
    public function processSearchByIdLcl()
    {
        $rates = ContractRateLclApi::where('contract_id', $this->id)->get();
        if (count($rates) == 0) {
            return response()->json(['message' => 'The requested contract is pending processing', 'state' => 'CONVERSION_PENDING'], 200);
        }
        $rates = $this->transformToArray($rates);
        return $rates;
    }

    public function transformToArray($rates)
    {
        $arr = array();
        foreach ($rates as $data) {
            $detalle = array($data->origin_port, $data->destiny_port, $data->via, $data->total, $data->minimum, $data->currency, $data->transit_time, $data->remarks);
            array_push($arr, $detalle);
        }

        return $arr;
    }

    /**
     * Scope a query to only include contracts by authenticated users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCurrentCompany($query)
    {
        // if (Auth::user()->hasRole('subuser')) {
        //     $company_id = Auth::user()->company_user_id;
        //     return $query->where('company_user_id', '=', $company_id)->where('user_id', '=', Auth::user()->id);
        // } else {
            $company_id = Auth::user()->company_user_id;
            return $query->where('company_user_id', '=', $company_id);
        // }
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
        return (new ContractLclFilter($request, $builder))->filter();
    }

    /**
     * Sync Contract User Restrictions
     *
     * @param  Array $users
     * @return void
     */
    public function ContractUsersRestrictionsSync($users)
    {
        DB::table('contractlcl_user_restrictions')->where('contractlcl_id', '=', $this->id)->delete();

        foreach ($users as $user_id) {
            ContractLclUserRestriction::create([
                'user_id' => $user_id,
                'contractlcl_id' => $this->id,
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
        DB::table('contractlcl_company_restrictions')->where('contractlcl_id', '=', $this->id)->delete();

        foreach ($companies as $company_id) {
            ContractLclCompanyRestriction::create([
                'company_id' => $company_id,
                'contractlcl_id' => $this->id,
            ]);
        }
    }

    /* Duplicate Contract Model instance with relations */
    public function duplicate()
    {

        $new_contract = $this->replicate();
        $new_contract->contract_code = null;
        $new_contract->name .= ' copy';
        $new_contract->save();

        $this->load('carriers.carrier', 'localcharges', 'rates');
        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {

                if ($relationRecord instanceof \App\LocalChargeLcl) {
                    $relationRecord->duplicate($new_contract->id);
                } else if ($relationRecord instanceof \App\ContractCarrierLcl) {
                    $newRelationship = $relationRecord->replicate();
                    $newRelationship->contract_id = $new_contract->id;
                    $newRelationship->save();
                } else {
                    $newRelationship = $relationRecord->replicate();
                    $newRelationship->contractlcl_id = $new_contract->id;
                    $newRelationship->save();
                }
            }
        }
        $new_contract->createCustomCode();
        return $new_contract;
    }

    public function createCustomCode()
    {
        $lastContract = ContractLcl::where('company_user_id', $this->company_user_id)
            ->whereNotNull('contract_code')->withTrashed()->orderBy('id', 'desc')->first();

        $company = strtoupper(substr($this->companyUser->name, 0, 3));

        $code = 'LCL-' . $company . '-1';

        if (!empty($lastContract)) {
            $lastContractId = intval(str_replace('LCL-' . $company . "-", "", $lastContract->contract_code));
            $code = 'LCL-' . $company . '-' . strval($lastContractId + 1);
        }

        $this->contract_code = $code;
        $this->save();
    }

    // Relationship for run files
    public function newcontractrequest(){
        return $this->hasOne('App\NewContractRequestLcl','contract_id','id');
    }
}