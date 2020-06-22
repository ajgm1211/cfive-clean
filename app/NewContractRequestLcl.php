<?php

namespace App;

use App\Notifications\N_general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class NewContractRequestLcl extends Model
{

    protected $table = 'new_contract_request_lcl';
    protected $fillable = [
        'namecontract',
        'numbercontract',
        'validation',
        'direction_id',
        'company_user_id',
        'namefile',
        'user_id',
        'updated',
        'time_star',
        'time_total',
        'time_manager',
        'time_star_one',
        'created',
        'created_at',
        'sentemail',
        'contract_id',
        'type',
        'data'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function direction()
    {
        return $this->belongsTo('App\Direction');
    }

    public function Requestcarriers()
    {
        return $this->hasMany('App\RequetsCarrierLcl', 'request_id');
    }

    public function companyuser()
    {
        return $this->belongsTo('App\CompanyUser', 'company_user_id');
    }

    public function ContractRequestCarrierSync($carriers, $api = false)
    {

        DB::table('request_lcl_carriers')->where('request_id', '=', $this->id)->delete();

        if ($api) {
            $carriers = explode(",", $carriers);
        }

        foreach ($carriers as $carrier) {
            RequetsCarrierLcl::create([
                'carrier_id' => $carrier,
                'request_id' => $this->id
            ]);
        }
    }

    /**
     * Notify a new request
     *
     * @param  Array  $admins
     * @return void
     */
    public function NotifyNewRequest($admins)
    {
        foreach ($admins as $userNotifique) {
            $userNotifique->notify(new N_general(Auth::user(), 'A new request has been created - ' . $this->id));
        }
    }
}
