<?php

namespace App;

use App\Notifications\N_general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class NewContractRequest extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $table = 'newcontractrequests';
    protected $fillable = [
        'namecontract',
        'numbercontract',
        'validation',
        'direction_id',
        'company_user_id',
        'namefile',
        'user_id',
        'created',
        'created_at',
        'time_star',
        'time_total',
        'time_manager',
        'time_star_one',
        'sentemail',
        'contract_id',
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
        return $this->hasMany('App\RequetsCarrierFcl', 'request_id');
    }

    public function companyuser()
    {
        return $this->belongsTo('App\CompanyUser', 'company_user_id');
    }

    /**
     * Sync Request Contract Carriers
     *
     * @param  Array  $carrier
     * @return void
     */
    public function ContractRequestCarrierSync($carriers, $api = false)
    {
        DB::table('request_fcl_carriers')->where('request_id', '=', $this->id)->delete();

        if ($api) {
            $carriers = explode(",", $carriers);
        }

        foreach ($carriers as $carrier_id) {
            RequetsCarrierFcl::create([
                'carrier_id' => $carrier_id,
                'request_id' => $this->id
            ]);
        }
    }

    /**
     * Notify a new request
     *
     * @param  Array  $carrier
     * @return void
     */
    public function NotifyNewRequest($admins)
    {
        foreach ($admins as $userNotifique) {
            $userNotifique->notify(new N_general(Auth::user(), 'A new request has been created - ' . $this->id));
        }
    }
}
