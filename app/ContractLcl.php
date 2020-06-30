<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class ContractLcl extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use \OwenIt\Auditing\Auditable;
    protected $table    = "contracts_lcl";
    protected $fillable = ['id', 'name', 'number', 'company_user_id', 'direction_id', 'account_id', 'validity', 'expire', 'status'];

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
    public function StoreInMedia($file)
    {
        $this->addMedia($file)->addCustomHeaders([
            'ACL' => 'public-read'
        ])->toMediaCollection('document', 'LclRequest');
    }
}
