<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\QuotaRequest;

class CompanyUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $casts = [
        'options' => 'array',
    ];

    protected $fillable = ['name', 'address', 'phone', 'currency_id', 'logo', 'pdf_language', 'type_pdf', 'pdf_ammounts', 'decimals'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function language()
    {
        return $this->hasOne('App\Language','id','pdf_language');
    }

    public function companyQuotes()
    {
        return $this->hasMany('App\QuoteV2');
    }

    public function quota()
    {
        return $this->hasOne('App\QuotaRequest');
    }

    public function getHigherId($companyCode)
    {
        $ids = [];
        $quotes = $this->companyQuotes()->get();
        if(count($quotes)==0){
            return count($quotes);
        } else {
            foreach($quotes as $q){
                $qid = intval(str_replace($companyCode."-","",$q->quote_id));
                array_push($ids,$qid);
            }
        }
        
        return max($ids);
    }

    public function settingsWhitelabel()
    {
        return $this->hasOne('App\SettingsWhitelabel');
    }

    /**
     * Create quantity of requests per company
     * 
     * @param mixed $data
     * 
     * @return void
     */
    public function createQuota($data){

        $due_date = $this->addMonthYearToDate($data->issued_date,$data->payment_type);

        QuotaRequest::updateOrCreate([
            'company_user_id' => $this->id
        ],[
            'type' => $data->type,
            'payment_type' => $data->payment_type,
            'quota' => $data->quota,
            'remaining_quota' => $data->remaining_quota,
            'company_user_id' => $this->id,
            'issued_date' => $data->issued_date,
            'due_date' => $due_date->format('Y-m-d'),
            'status' => $data->status,
        ]);
    }

    /**
     * Format date to add month or year
     * 
     * @param mixed $data
     * 
     * @return date
     */
    public function addMonthYearToDate($date, $type){

        $date = Carbon::createFromFormat('Y-m-d', $date);

        if($type=='monthly'){
            $due_date = $date->addMonth();
        }else if($type=='biannual'){
            $due_date = $date->addMonths(6);
        }else{
            $due_date = $date->addYear();
        }

        return $due_date;
    }
}
