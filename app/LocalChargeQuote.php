<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeQuote extends Model
{
    protected $fillable = ['price', 'profit', 'total', 'charge', 'surcharge_id', 'calculation_type_id', 'currency_id', 'port_id', 'quote_id', 'type_id'];

    protected $casts = [
        'price' => 'array',
        'profit' => 'array',
        'total' => 'array',
    ];

    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function surcharge()
    {
        return $this->belongsTo('App\Surcharge');
    }

    public function calculation_type()
    {
        return $this->belongsTo('App\CalculationType');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function sumarize()
    {
        $quote = $this->quotev2()->first();

        $equip = $quote->getContainerCodes($quote->equipment);

        $equip_array = explode(',', $equip);

        array_splice($equip_array, -1, 1);

        $totals = [];

        foreach ($equip_array as $eq) {
            $totals['c' . $eq] = 0;
        }

        if ($this->price != null) {
            foreach ($equip_array as $eq) {
                foreach ($this->price as $key => $price) {
                    if ($key == 'c' . $eq) {
                        $totals[$key] += $price;
                    }
                }
            }
        }

        if ($this->profit != null) {
            foreach ($equip_array as $eq) {
                foreach ($this->profit as $k => $profit) {
                    if ($k == 'm' . $eq) {
                        $clear_key = str_replace('m', 'c', $k);
                        $totals[$clear_key] += $profit;
                    }
                }
            }
        }
        
        $this->update(['total' => $totals]);
    }

    public function totalize()
    {
        $quote = $this->quotev2()->first();

        LocalChargeQuoteTotal::where('quote_id', $quote->id)->delete();

        $charges = $this->where('quote_id', $quote->id)->get();

        $equip = $quote->getContainerCodes($quote->equipment);

        $equip_array = explode(',', $equip);

        array_splice($equip_array, -1, 1);

        $totals = [];

        foreach ($equip_array as $eq) {
            $totals['c' . $eq] = 0;
        }

        foreach($charges as $charge){
            if ($charge->total != null) {
                foreach ($equip_array as $eq) {
                    foreach ($charge->total as $key => $total) {
                        if ($key == 'c' . $eq) {
                            $totals[$key] += $total;
                        }
                    }
                }
            }
        }

        LocalChargeQuoteTotal::create([
            'total' => $totals,
            'quote_id' => $quote->id,
        ]);
        
    }
}
