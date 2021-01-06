<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Traits\QuoteV2Trait;

class AutomaticRateTotal extends Model
{
    use QuoteV2Trait;

    protected $casts = [
        'markups' => 'json',
        'total' => 'json'
    ];

    protected $fillable = [
        'id', 'quote_id', 'markups', 'currency_id', 'totals', 'automatic_rate_id','origin_port_id', 'destination_port_id'];

    public function quote()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency', 'id', 'currency_id');
    }

    public function rate()
    {
        return $this->belongsTo('App\AutomaticRate', 'automatic_rate_id');
    }

    public function origin_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'destination_port_id');
    }

    public function scopeGetQuote($query, $id)
    {
        return $query->where('quote_id', $id);
    }

    public function duplicate($quote)
    {
        $newRecord = $this->replicate();
        $newRecord->quote_id = $quote->id;
        $newRecord->save();

        return $newRecord;
    }

    public function totalize($newCurrencyId)
    {
        //getting all data needed to calculate totals
        $quote = $this->quote()->first();

        $rate = $this->rate()->first();

        if ($quote->type == 'FCL') {
            $equip = $quote->getContainerCodes($quote->equipment);

            $equipArray = explode(',', $equip);

            array_splice($equipArray, -1, 1);

            $charges = $rate->charge()->where([['surcharge_id', '!=', null], ['type_id', 3]])->get();

            $oceanFreight = $rate->charge()->where('surcharge_id', null)->first();

            $this->update(['currency_id' => $newCurrencyId]);

            $currency = $this->currency()->first();
            
            $totals = [];

            foreach ($equipArray as $eq) {
                $totals['c' . $eq] = 0;
            }

            // adding all charges together
            foreach ($charges as $charge) {
                $amountObject = json_decode($charge->amount);
                $chargeCurrency = $charge->currency()->first();
                foreach($amountObject as $key=>$value){
                    @$amountArray[$key] = $value;
                }
                $amountArray = $this->convertToCurrency($chargeCurrency,$currency,$amountArray);
                foreach($amountArray as $key=>$value){
                    @$totals[$key] += isDecimal($value,true);
                }
            }

            //adding autorate markups
            if ($this->markups != null) {
                $markups = $this->markups;
                foreach ($markups as $mark => $profit) {
                    @$markups[$mark] = isDecimal($profit,true);
                    $totals[str_replace('m', 'c', $mark)] += isDecimal($profit,true);
                }
            }else{
                $markups = null;
            }
            
            //adding ocean freight
            if (isset($oceanFreight) && $oceanFreight->amount != null) {
                $freight_amount = json_decode($oceanFreight->amount);
                foreach ($freight_amount as $fr => $am) {
                    @$totals[$fr] += round($am, 2);
                    @$totals[$fr] = isDecimal($totals[$fr], true);
                }
            }

            $totalsJson = json_encode($totals);

            $this->update(['totals' => $totalsJson, 'markups' => $markups]);
            $rate->update(['total' => $totalsJson]);

        } else if ($quote->type == 'LCL') {

            $charges = $rate->charge_lcl_air()->where([['surcharge_id', '!=', null], ['type_id', 3]])->get();

            $oceanFreight = $rate->charge_lcl_air()->where('surcharge_id', null)->first();

            $this->update(['currency_id' => $newCurrencyId]);

            $currency = $this->currency()->first();

            $totals = [];
            $totals['total'] = 0;
            $totals['per_unit'] = 0;
            $markups = [];
            $markups['per_unit'] = 0;
            $markups['total'] = 0;

            // adding all charges together
            foreach ($charges as $charge) {
                $chargeCurrency = $charge->currency()->first();
                $partials = [];
                $partials['total'] = $charge->total;
                $partials['per_unit'] = $charge->price_per_unit;
                $partials = $this->convertToCurrency($chargeCurrency,$currency,$partials);
                foreach($partials as $key=>$amount){
                    @$totals[$key] += $amount;
                }
                /**if($charge->markup){
                    $chargeUnits = $charge->units;
                    $partialMarkups = [];
                    $partialMarkups['per_unit'] = $charge->markup;
                    $partialMarkups['total'] = $partialMarkups['per_unit'] * $chargeUnits;
                    $partialMarkups = $this->convertToCurrency($chargeCurrency,$currency,$partialMarkups);
                    foreach($partialMarkups as $key=>$amount){
                        @$markups[$key] += $amount;
                    }
                }**/
            }

            //adding ocean freight
            $freightUnits = @$oceanFreight ->units;
            $freightPerUnit = @$oceanFreight->price_per_unit;
            $freightAmount = @$oceanFreight->total;
            $totals['total'] += $freightAmount;
            $totals['per_unit'] += $freightPerUnit;
            $totals['total'] = isDecimal($totals['total'], true);
            $totals['per_unit'] = isDecimal($totals['per_unit'], true);
            /**if($oceanFreight->markup){
                $freightMarkup = $oceanFreight->markup;
                $markups['per_unit'] += isDecimal($freightMarkup,true);
                $markups['total'] += isDecimal($markups['per_unit'] * $freightUnits,true);
                $totals['total'] += $markups['total'];
                $totals['per_unit'] += $markups['per_unit'];
            }**/

            //adding markups
            if($this->markups){
                foreach($this->markups as $key=>$mark){
                    $markups[$key] = isDecimal($mark,true);
                }
                $markups['total'] = $markups['per_unit'] * $freightUnits;
                $totals['per_unit'] += $markups['per_unit'];
                $totals['total'] += $markups['total'];
            }

            $totals = json_encode($totals);

            $this->update(['totals' => $totals, 'markups' => $markups]);
            $rate->update(['total' => $totals]);
        }
    }
}
