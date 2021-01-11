<?php

namespace Tests\Unit;

use App\charge;
use App\QuoteV2;
use Tests\TestCase;
use App\Automaticrate;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;


class QuotesFclTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_create_Quote()
    {

        $this->withoutExceptionHandling();
        $quote= factory(QuoteV2::class)->create();

    
        $AutoRate = new Automaticrate();
        $AutoRate->quote_id           = $quote->id;
        $AutoRate->contract           = '';
        $AutoRate->validity_start     = '2021-01-30';
        $AutoRate->validity_end       = '2021-12-31';
        $AutoRate->origin_port_id     = 949;
        $AutoRate->destination_port_id= 950;
        $AutoRate->carrier_id         = 25;
        $AutoRate->currency_id        = 149;
        // $AutoRate->total              = "{\"c20DV\":\"20\",\"c40DV\":\"20\",\"c40HC\":\"20\"}";
        $AutoRate->save();
        
       

        $charges = new charge();
        $charges->automatic_rate_id   = $AutoRate->id;
        $charges->type_id             = 3;
        // $charges->surcharge_id        = 3;
        $charges->calculation_type_id = 5;
        $charges->amount              = "{\"c20DV\":\"115\",\"c40DV\":\"214\",\"c40HC\":\"214\"}";
        $charges->currency_id         = 149;
        $charges->save();

        $charges = new charge();
        $charges->automatic_rate_id   = $AutoRate->id;
        $charges->type_id             = 3;
        $charges->surcharge_id        = 3;
        $charges->calculation_type_id = 5;
        $charges->amount              = "{\"c20DV\":\"115\",\"c40DV\":\"214\",\"c40HC\":\"214\"}";
        $charges->currency_id         = 149;
        $charges->save();

        $charges = new charge();
        $charges->automatic_rate_id   = $AutoRate->id;
        $charges->type_id             = 2;
        $charges->surcharge_id        = 3;
        $charges->calculation_type_id = 5;
        $charges->amount              = "{\"c20DV\":\"20\",\"c40DV\":\"20\",\"c40HC\":\"20\"}";
        $charges->currency_id         = 149;
        $charges->markups             = "{\"m20DV\":\"20\",\"m40DV\":\"20\",\"m40HC\":\"20\"}";
        $charges->save();
        
        $localcharge= new LocalChargeQuote();
        $localcharge->price =[
            "c20DV "=> 20,
            "c40DV "=>  20,
            "c40HC "=> 20,
        ];
        json_encode($localcharge->price);
        $localcharge->type_id=2;
        $localcharge->total=[
            "c20DV "=> 40,
            "c40DV "=>  40,
            "c40HC "=> 40
        ];
        json_encode($localcharge->total);
        $localcharge->profit=[
            "m20DV "=> 20,
            "m40DV "=>  20,
            "m40HC "=> 20
        ];
        json_encode($localcharge->profit);
        $localcharge->charge="julio";
        $localcharge->surcharge_id=$charges->surcharge_id;
        $localcharge->calculation_type_id=$charges->calculation_type_id;
        $localcharge->currency_id=149;            
        $localcharge->port_id=950;
        $localcharge->quote_id=$quote->id;
        $localcharge->save();

        $localchargeT=new LocalChargeQuoteTotal();
        $localchargeT->total=[
            "c20DV "=> 40,
            "c40DV "=>  40,
            "c40HC "=> 40
        ];
        json_encode($localchargeT->total);
        $localchargeT->type_id=2;
        $localchargeT->currency_id=149;            
        $localchargeT->port_id=950;
        $localchargeT->quote_id=$quote->id;
        $localchargeT->save();

        $this->seeInDatabase('quote_v2s',['quote_id' => $quote->quote_id]);
        // $this->seeInDatabase('quote_v2s',['automatic_rates' => $AutoRate->id]);
        // $this->seeInDatabase('quote_v2s',['Charges' => $charges->id]);
    }
} 
