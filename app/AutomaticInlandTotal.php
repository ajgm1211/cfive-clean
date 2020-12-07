<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Traits\QuoteV2Trait;

class AutomaticInlandTotal extends Model
{
    use QuoteV2Trait;

    protected $appends = ['calculation_type' => 1];

    protected $fillable = ['quote_id','port_id','currency_id','totals','markups','type','inland_address_id'];

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2','quote_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }

    public function inland_address()
    {
        return $this->hasOne('App\InlandAddress','id','inland_address_id');
    }

    public function get_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'port_id');
    }

    public function port()
    {
        return $this->hasOne('App\Harbor', 'id', 'port_id');
    }

    public function totalize()
    {
        $quote = $this->quotev2()->first();

        $company_user = CompanyUser::find(\Auth::user()->company_user_id);
    
        $currency = $company_user->currency()->first();

        if($quote->type=='FCL'){

            $equip = $quote->getContainerCodes($quote->equipment);
    
            $equip_array = explode(',',$equip);
    
            array_splice($equip_array,-1,1);
        
            $inlands = AutomaticInland::where([
                ['quote_id',$this->quote_id],
                ['port_id',$this->port_id],
                ['type',$this->type],
                ['inland_address_id',$this->inland_address_id]])->get();
    
            $markups = [];
            $totals = [];
    
            foreach($equip_array as $eq){
                $totals['c'.$eq] = isDecimal(0,true);
                $markups['m'.$eq] = isDecimal(0,true);
            }
            
            foreach($inlands as $inland){
                $amount_object = json_decode($inland->rate);
                $amount_array = [];
                foreach($amount_object as $key=>$value){
                    $amount_array[$key] = $value;
                }
                $inland_currency = $inland->currency()->first();
                $amount_array = $this->convertToCurrency($inland_currency,$currency,$amount_array);
                foreach($amount_array as $key=>$value){
                    $totals[$key] += isDecimal($value,true);
                }
                if($inland->markup){
                    $markup_object = json_decode($inland->markup);
                    $markup_array = [];
                    foreach($markup_object as $key=>$value){
                        $markup_array[$key] = $value;
                    }
                    $markup_array = $this->convertToCurrency($inland_currency,$currency,$markup_array);
                    foreach($markup_array as $key=>$value){
                        $markups[$key] += isDecimal($value,true);
                        $totals['c'.str_replace('m','',$key)] += $value;
                    }
                }
            }
    
            $totals = json_encode($totals);
            $markups = json_encode($markups);
            
            $this->update(['totals'=>$totals,'markups'=>$markups]);
        }else if($quote->type=='LCL'){
        
            $inlands = AutomaticInlandLclAir::where([
                ['quote_id',$this->quote_id],
                ['port_id',$this->port_id],
                ['type',$this->type],
                ['inland_address_id',$this->inland_address_id]])->get();
    
            $totals['lcl_totals'] = 0;
            $markups['profit'] = 0;
            $inlandCharges = [];

            foreach($inlands as $inland){
                $inlandCharges[0] = $inland->total;
                $inlandCurrency = $inland->currency()->first();
                if($inland->markup){
                    $inlandCharges[1] = $inland->markup;
                }
                $inlandCharges = $this->convertToCurrency($inlandCurrency,$currency,$inlandCharges);
                $full = $inlandCharges[0] + $inlandCharges[1];
                $totals['lcl_totals'] += isDecimal($full,true);
                $markups['profit'] += isDecimal($inlandCharges[1],true);
            }
    
            $totalsPrice = json_encode($totals);
            $totalsMarkup = json_encode($markups);
            
            $this->update(['totals'=>$totalsPrice]);
            $this->update(['markups'=>$totalsMarkup]);
        }
    }

    public function scopeQuotation($query, $quote)
    {
        return $query->where('quote_id', $quote);
    }

    public function scopePort($query, $port)
    {
        return $query->where('port_id', $port);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getTotalAttribute($array)
    {
        $array = json_decode($array);

        return $array;
    }

    public function scopeConditionalPort($q, $port)
    {
        return $q->when($port, function ($query, $port) {
            return $query->where('port_id', $port);
        });
    }
}
