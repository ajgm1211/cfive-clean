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
use App\User;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;

class ContractLcl extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use SearchTraitApi;
    use UtilTrait;
    use \OwenIt\Auditing\Auditable;
    protected $table    = "contracts_lcl";
    protected $fillable = ['id', 'name', 'number', 'company_user_id','user_id', 'direction_id', 'account_id', 'validity', 'expire', 'status', 'code', 'is_manual', 'is_api'];

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
        if(count($rates)==0){
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
            array_push($arr,$detalle);
        }

        return $arr;
    }
}
