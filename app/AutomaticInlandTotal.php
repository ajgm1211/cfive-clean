<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AutomaticInlandTotal extends Model
{
    protected $fillable = ['quote_id','port_id','currency_id','totals','markups','type'];

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2','quote_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }

    public function totalize()
    {
        $quote = $this->quotev2()->first();

        $equip = $quote->getContainerCodes($quote->equipment);

        $equip_array = explode(',',$equip);

        array_splice($equip_array,-1,1);

        $currency = $this->currency()->first();

        $inlands = AutomaticInland::where([['quote_id',$this->quote_id],['port_id',$this->port_id],['type',$this->type]])->get();

        $totals_usd = [];

        foreach($equip_array as $eq){
            $totals_usd['c'.$eq] = 0;
        }
        
        foreach($inlands as $inland){
            $amount_array = json_decode($inland->rate);
            $inland_currency = $inland->currency()->first();
            foreach($amount_array as $key=>$value){
                if($inland_currency->alphacode != 'USD'){
                    $inland_conversion = $inland_currency->rates;
                    $value /= $inland_conversion;
                    $value = round($value,2);
                }
                $totals_usd[$key] += $value;
            }
        }

        if($currency->alphacode != 'USD'){
            $conversion = $currency->rates;
            foreach($totals_usd as $cont=>$price){
                $conv_price = $price*$conversion;
                $totals_usd[$cont] = round($conv_price,2);
            }
        }

        if($this->markups != null){
            $markups = json_decode($this->markups);
            foreach($markups as $mark=>$profit){
                $clear_key = str_replace('m','c',$mark);
                $totals_usd[$clear_key] += $profit;
            }
        }

        $totals = json_encode($totals_usd);
        
        $this->update(['totals'=>$totals]);
    }
}
