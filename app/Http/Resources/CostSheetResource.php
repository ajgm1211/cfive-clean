<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\LocalChargeQuote;
use App\LocalChargeQuoteLcl;
use App\ChargeLclAir;
use App\Charge;
use App\Currency;
use App\Http\Traits\QuoteV2Trait;

class CostSheetResource extends JsonResource
{
    use QuoteV2Trait;
    
    private $quote; 
    private $autorate;
    private $header_fields;
    private $currencyToReport; 
    private $buyingAmountAll;
    private $sellingAmountAll;
    private $freightCharges;
    private $localesBuying;
    private $localesSelling;
    private $inlandsBuying;
    private $inlandsSelling;

    public function __construct($quote, $autorate)
    {   
        $this->quote = $quote;        
        $this->autorate = $autorate;
        $this->header_fields = $this->getHeaderFields();
        $this->currencyToReport = Currency::find($quote->pdf_options['totalsCurrency']['id']);
        
        // Variables para acumular montos para hallar totales
        $this->buyingAmountAll = [];
        $this->sellingAmountAll = [];

        // para guardar freights
        $this->freightCharges = [];

        // para guardar los local correspondientes
        $this->localesBuying = [];
        $this->localesSelling = [];

        // para guardar los inland correspondientes
        $this->inlandsBuying = [];
        $this->inlandsSelling = [];
    }

    public function toArray($request)
    {   
        $dataFeatCostSheet = [];

        $dataFeatCostSheet['containers_head'] = $this->header_fields;   
        $dataFeatCostSheet['type'] = $this->quote->type; 

        $ratesBuying = [];    
        $ratesSelling = [];    
        $profit = [];

        // Obtener inlands
        $inlands = $this->getInlandsModel();        
        $this->buildInlandsData($inlands);  
        
        // Obtenemos los charges
        $charges_model = $this->getChargesModel();
        $this->buildChargesData($charges_model);   

        // Obtenemos los local charges
        $local_charges_model = $this->getLocalChargesModel();
        $this->buildLocalChargesData($local_charges_model);  

        // Obtener freight charges
        $freightChargesModel = $this->getChargesFreightModel();
        $this->buildFreightChargesData($freightChargesModel);   
        
        array_push($ratesBuying, [
            'freight' => $this->freightCharges,
            'locales' => $this->localesBuying,
            'inlands' => $this->inlandsBuying,
            'totals' => $this->getSumaAmountTotals($this->buyingAmountAll)
        ]);
    
        array_push($ratesSelling, [
            'total_freight' => $this->getAmountPerContainer($this->convertToCurrencyQuote(
                $this->autorate->currency, 
                $this->currencyToReport,
                $this->getFreightChargeTotalArray(),
                $this->quote
            )),
            'locales' => $this->localesSelling,
            'inlands' => $this->getInlandsTotal($this->inlandsSelling),
            'totals' => $this->getSumaAmountTotals($this->sellingAmountAll)
        ]);

        $arrayProfit = $this->calculateProfit($this->sellingAmountAll, $this->buyingAmountAll);
        
        $arrayPercentageProfit = $this->calculatePercentage(
            $arrayProfit, 
            $this->getSumaAmountTotals($this->sellingAmountAll)
        );

        array_push($profit, [
            'profit' => $arrayProfit,
            'profit_percentage' => $arrayPercentageProfit
        ]);

        $dataRate = [];        

        array_push($dataRate, [
            'automatic_rate_id' => $this->autorate->id,
            'currency' => ['currency_id' => $this->currencyToReport->id, 'alphacode' => $this->currencyToReport->alphacode],
            'POL' => ['id' => $this->autorate->origin_port_id, 'name' => $this->autorate->origin_port->name],  
            'POD' => ['id' => $this->autorate->destination_port_id, 'name' => $this->autorate->destination_port->name],
            'carrier' => ['id' => $this->autorate->carrier_id, 'name' => $this->autorate->carrier->name],
            'buying' => $ratesBuying,
            'selling' => $ratesSelling,
            'profit' => $profit
        ]);
        
        $dataFeatCostSheet['rates'] = $dataRate;         
        
        //dd($dataFeatCostSheet);
        return $dataFeatCostSheet;
    }

    public function buildFreightChargesData($freightChargesModel) {

        foreach ($freightChargesModel as $charge) { 
            
            // Acumular para calcular subtotal de compra. Se debe enviar montos por contenedor convertidos al tipo de moneda del rate            
            $convertToCurrencyfreightAmountsCharges = $this->convertToCurrencyQuote(
                $charge->currency , 
                $this->currencyToReport,
                $this->getFreightChargePriceArray($charge),
                $this->quote
            );   
            
            array_push($this->freightCharges, [
                'type' => $charge->type->description, 
                'surcharge' => $charge->surcharge->name, 
                'currency' => ['currency_id' => $this->currencyToReport->id, 'alphacode' => $this->currencyToReport->alphacode],
                'amount' => $this->getAmountPerContainer($convertToCurrencyfreightAmountsCharges)
            ]); 

            if ($this->quote->type == 'FCL') {
                array_push($this->buyingAmountAll, $this->getAmountPerContainer($convertToCurrencyfreightAmountsCharges));              
            }
            if ($this->quote->type == 'LCL') {
                array_push($this->buyingAmountAll, $convertToCurrencyfreightAmountsCharges['amount']);              
            }
        }
        
        $convertToCurrencyfreightAmountSelling = $this->convertToCurrencyQuote(
            $this->autorate->currency, 
            $this->currencyToReport,
            $this->getFreightChargeTotalArray(),
            $this->quote
        );

        // acumular para calcular subtotal de venta
        if ($this->quote->type == 'FCL') {
            array_push($this->sellingAmountAll, $this->getAmountPerContainer($convertToCurrencyfreightAmountSelling));
        }
        if ($this->quote->type == 'LCL') {
            array_push($this->sellingAmountAll, $convertToCurrencyfreightAmountSelling['amount']);
        }
    }

    public function buildLocalChargesData($local_charges_model) {

        foreach ($local_charges_model as $local) {
            
            // Solo locales donde su port coindicen conel origen o destino del rate.
            if ($local->port_id == $this->autorate->origin_port_id || $local->port_id == $this->autorate->destination_port_id) {

                // Convertimos el/los monto(s) del local charge al tipo de moneda del rate correspondiente. 
                $convertToCurrencylocalCharge = $this->convertToCurrencyQuote(
                    $local->currency, 
                    $this->currencyToReport,
                    $this->getLocalTotalArray($local),
                    $this->quote
                );

                // Asignamos campos de local charges a mostrar
                array_push($this->localesSelling, [
                    'type' => $local->type->description,
                    'surcharge' => $local->charge,
                    'currency' => ['currency_id' => $this->currencyToReport->id, 'alphacode' => $this->currencyToReport->alphacode],
                    'amount' => $this->getLocalChargeSameCoin($convertToCurrencylocalCharge)
                ]);


                //Una vez convertido al tipo de moneda del rate se asigna a un arreglo para acumular la suma.
                array_push($this->sellingAmountAll, $this->getLocalChargeSameCoin($convertToCurrencylocalCharge));
            }
        }
    }
    public function buildChargesData($charges_model) {

        foreach ($charges_model as $charge) {

            // Convertimos el/los monto(s) de los charges al tipo de moneda del rate correspondiente. 
            $convertToCurrencyCharge = $this->convertToCurrencyQuote(
                $charge->currency, 
                $this->currencyToReport,
                $this->getChargeArray($charge),
                $this->quote
            );

            // Asignamos campos de charges a mostrar
            array_push($this->localesBuying, [ 
                'type' => $charge->type->description,
                'surcharge' => $charge->surcharge->name,
                'currency' => ['currency_id' => $this->currencyToReport->id, 'alphacode' => $this->currencyToReport->alphacode],
                'amount' => $this->getChargeSameCoin($convertToCurrencyCharge)
            ]);


            //Una vez convertido al tipo de moneda del rate se asigna a un arreglo para acumular la suma.
            array_push($this->buyingAmountAll, $this->getChargeSameCoin($convertToCurrencyCharge));
        }
    }
    public function buildInlandsData($inlands) {
    
        if ($this->quote->type == 'FCL') {
            $totalInland = [];
        }
        if ($this->quote->type == 'LCL') {
            if(count($inlands) == 0) {
                $totalInland = [];
            } else {
                $totalInland = ['amount' => 0];
            }
        }
        foreach ($inlands as $inland) {
            if ($inland->port_id == $this->autorate->origin_port_id || $inland->port_id == $this->autorate->destination_port_id) {
                
                $convertToCurrencyInlandRate = $this->convertToCurrencyQuote(
                    $inland->currency, 
                    $this->currencyToReport,
                    $this->getInlandRateArray($inland),
                    $this->quote
                );

                array_push($this->inlandsBuying, [
                    'type' => $inland->type,
                    'charge' => $inland->charge,
                    'currency' => ['currency_id' => $this->currencyToReport->id, 'alphacode' => $this->currencyToReport->alphacode], 
                    'rate' => $this->getAmountPerContainer($convertToCurrencyInlandRate)
                ]);        
                
                $convertToCurrencyInlandMarkup = $this->convertToCurrencyQuote(
                    $inland->currency, 
                    $this->currencyToReport,
                    $this->getInlandMarkupArray($inland), 
                    $this->quote
                );

                // acumular para calcular subtotal de compra
                if ($this->quote->type == 'FCL') {
                    array_push($this->buyingAmountAll, $this->getAmountPerContainer($convertToCurrencyInlandRate));
                    
                    // Valor original(rate) + profit(markup) de todos los inland asociados a los puertos del flete 
                    foreach ($convertToCurrencyInlandRate as $keyRate => $valRate) {                          
                        foreach ($convertToCurrencyInlandMarkup as $keyMarkup => $valMarkup) { 
                            if ($keyRate === str_replace("m", "c", $keyMarkup)) { 
                                if (!isset($totalInland[$keyRate])) { 
                                    $totalInland[$keyRate] = 0;
                                }
                                $totalInland[$keyRate] = $totalInland[$keyRate] + $valMarkup + $valRate; 
                            }
                        }                        
                    }                    
                }
                if ($this->quote->type == 'LCL') {
                    array_push($this->buyingAmountAll, $convertToCurrencyInlandRate['amount']);
                    //Se suma los valores de todos los inland (total + markup) para venta
                    $totalInland['amount'] = $totalInland['amount'] + $convertToCurrencyInlandRate['amount'] + $convertToCurrencyInlandMarkup['amount'];
                }
            } 
        }
        
        if (sizeof($totalInland) > 0) {
            // acumular para calcular subtotal de venta
            array_push($this->inlandsSelling, $this->getAmountInlandsTotal($totalInland));
            if ($this->quote->type == 'FCL') {
                array_push($this->sellingAmountAll, $this->getAmountPerContainer($totalInland));
            }
            if ($this->quote->type == 'LCL') {
                array_push($this->sellingAmountAll, $totalInland['amount']);   
            }
        }
        
    }

    public function getHeaderFields() {
        $headerFields = [];        

        if ($this->quote->type == 'LCL') {
            $headerFields = ['name' => 'Price'];
        } else {
            $equipments = $this->quote->getContainersFromEquipment($this->quote->equipment);
            
            foreach ($equipments as $equipment) {
                array_push($headerFields, ['name' => $equipment->name]);
            }
        }
        return $headerFields; 
    }

    public function getAmountPerContainer($array) {
        
        $amountPerContainer = [];
        foreach ($this->header_fields as $container){ 
            $amount = "0.00";
            if($array){      
                if ($this->quote->type == 'LCL') {
                    $amount = $array['amount'];
                } else {
                    foreach ($array as $clave => $val) {                     
                        if('c'.str_replace(' ', '', $container['name']) == $clave) {
                            $amount = $val;
                        }                     
                    }       
                }
            }                
            if ($this->quote->type == 'LCL') {
                array_push($amountPerContainer, ['name' => $container, 'amount' => round((float)$amount, 2)]);              
            } else { 
                array_push($amountPerContainer, ['name' => $container['name'], 'amount' => round((float)$amount, 2)]);              
            }
        } 
        if ($this->quote->type == 'FCL') {
            return $amountPerContainer;
        }
        else {
            return $amountPerContainer[0]['amount'];
        }
    }

    public function sumaAmountPerContainer($array) {
        $total = $array[0];
        $cantContainers = count($total);
        for ($i = 1; $i < count($array); $i++) {
            for ($j = 0; $j < $cantContainers; $j++) {   
                $total[$j]['amount'] = round($total[$j]['amount'] + $array[$i][$j]['amount'], 2);                
            }   
        }
        return $total;
    }

    public function diffAmountPerContainer($arrayA, $arrayB) {
        $diff = $arrayA;
        $cantContainers = count($arrayA);        
        for ($j = 0; $j < $cantContainers; $j++) {   
            $diff[$j]['amount'] = round($arrayA[$j]['amount'] - $arrayB[$j]['amount'], 2);                
        }
        return $diff;
    }

    public function calculatePercentage($a, $b) {    
        if ($this->quote->type == 'FCL') {
            $percentage = $a;
            $cantContainers = count($a);  
            for ($j = 0; $j < $cantContainers; $j++) {   
                if($b[$j]['amount'] == 0) {
                    $dec = 0;                
                } else {
                    $dec = $a[$j]['amount'] / $b[$j]['amount'];
                }

                $percentage[$j]['amount'] = $this->formatPercentage($dec);
            }
            return $percentage;
        }
        if ($this->quote->type == 'LCL') {
            if($b == 0) {
                return 0;
            } else {
                return $this->formatPercentage($a/$b);
            }
        }
    }

    public function formatPercentage($num) {
        return number_format($num * 100, 2, ",", ".")." %";
    }

    public function getLocalChargesModel() {
        if ($this->quote->type == 'FCL') {
            return LocalChargeQuote::where('quote_id', $this->quote->id)->get();
        }
        if ($this->quote->type == 'LCL') {
            return LocalChargeQuoteLcl::where('quote_id', $this->quote->id)->get();
        }
    }

    public function getInlandsModel() {
        if ($this->quote->type == 'FCL') {
            $inlands = $this->quote->inland()->get();
        } else if ($this->quote->type == 'LCL') {
            $inlands = $this->quote->inland_lcl()->get();
        }
        return $inlands;
    }

    public function getChargesFreightModel() {
        if ($this->quote->type == 'FCL') {
            return Charge::where('type_id',3)
                    ->filterByAutorate($this->autorate->id)
                    ->get();        
        }
        if ($this->quote->type == 'LCL') {
            return $this->autorate->charge_lcl_air->where('type_id',3);
        }
    }

    public function convertToArray($data) {
        $amount_array = [];
        if (isset($data)) { 
            if(!sizeof($data) == 0) {
                foreach($data as $key=>$value){
                    $amount_array[$key] = $value;
                }
            }
        }
        return $amount_array;
    }

    public function getCharge($charge) {
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer(json_decode($charge['amount'],true));
        }
        if ($this->quote->type == 'LCL') {
            return $charge->units * $charge->price_per_unit;
        }        
    }

    public function getChargeArray($charge) {
        if ($this->quote->type == 'FCL') {
            return $this->convertToArray(json_decode($charge['amount'],true));
        }
        if ($this->quote->type == 'LCL') {
            return ['amount' => $charge->units * $charge->price_per_unit];
        }  
    }

    public function getLocalTotal($local) {
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer($local['total']);
        }
        if ($this->quote->type == 'LCL') {
            return $local->total;
        }        
    }

    public function getLocalTotalArray($local) {
        if ($this->quote->type == 'FCL') {
            return $this->convertToArray($local['total']);
        }
        if ($this->quote->type == 'LCL') {
            return ['amount' => $local->total];
        } 
    }   
    
    public function getFreightChargePriceArray($charge) { 
        if ($this->quote->type == 'FCL') {
            return $this->convertToArray(json_decode($charge['amount'],true));
        }
        if ($this->quote->type == 'LCL') {
            return ['amount' => $charge->total];
        }         
    }

    public function getFreightChargePrice($charge) { 
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer(json_decode($charge['amount'],true));
        }
        if ($this->quote->type == 'LCL') {
            return $charge->total;
        }
    }

    public function getFreightChargeTotal() {
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer(json_decode($this->autorate->total,true));
        }
        if ($this->quote->type == 'LCL') {
            $totalArray = json_decode($this->autorate->total,true);
            return $totalArray['total'];         
        }
    }

    public function getFreightChargeTotalArray() {
        if ($this->quote->type == 'FCL') {
            return $this->convertToArray(json_decode($this->autorate->total,true));
        }
        if ($this->quote->type == 'LCL') {
            $total = json_decode($this->autorate->total,true);
            return ['amount' => $total['total']]; 
        }
    }

    public function getSumaAmountTotals($data) {
        if ($this->quote->type == 'FCL') {
            return $this->sumaAmountPerContainer($data);
        }
        if ($this->quote->type == 'LCL') {
            return array_sum($data);
        }        
    }
    
    public function calculateProfit($selling, $buying) {
        if ($this->quote->type == 'FCL') {
            return $this->diffAmountPerContainer(
                $this->sumaAmountPerContainer($selling), 
                $this->sumaAmountPerContainer($buying)
            );
        }
        if ($this->quote->type == 'LCL') {
            return  round((float)array_sum($selling) - (float)array_sum($buying), 2);
        }
    }

    public function getInlandRate($inland) { 
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer(json_decode($inland['rate'],true));
        }
        if ($this->quote->type == 'LCL') {
            return $inland->total; 
        }
    }

    public function getInlandRateArray($inland) { 
        if ($this->quote->type == 'FCL') {
            return $this->convertToArray(json_decode($inland['rate'],true));
        }
        if ($this->quote->type == 'LCL') {
            return ['amount' => $inland->total];
        }         
    }

    public function getInlandMarkupArray($inland) { 
        if ($this->quote->type == 'FCL') {
            return $this->convertToArray(json_decode($inland['markup'],true));
        }
        if ($this->quote->type == 'LCL') {
            return ['amount' => $inland->markup];
        }         
    }

    public function getAmountInlandsTotal($data) {
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer($data);
        }
        if ($this->quote->type == 'LCL') {
            return $data['amount'];
        }
    }

    public function getInlandsTotal($data) { 
        if ($this->quote->type == 'FCL') { 
            return $data; 
        }
        if ($this->quote->type == 'LCL') {
            if (isset($data)) { 
                if (!sizeof($data) == 0) {
                    return $data[0];
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
            
        }
    }

    public function getLocalChargeSameCoin($data) {
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer($data);
        }
        if ($this->quote->type == 'LCL') {
            return $data['amount'];
        }
    }

    public function getChargeSameCoin($data) {
        if ($this->quote->type == 'FCL') {
            return $this->getAmountPerContainer($data);
        }
        if ($this->quote->type == 'LCL') {
            return $data['amount'];
        }
    }

    public function getChargesModel() {
        if ($this->quote->type == 'FCL') {
            $originCollection = Charge::select('*')
                ->where('type_id', 1)
                ->whereHas('automatic_rate', function ($q) {
                    $q
                    ->whereIn('origin_port_id', [$this->autorate->origin_port_id, $this->autorate->destination_port_id])
                    ->where('quote_id', $this->quote->id);
                })->get();
            $destinyCollection = Charge::select('*')
                ->where('type_id', 2)
                ->whereHas('automatic_rate', function ($q) {
                    $q
                    ->whereIn('destination_port_id', [$this->autorate->origin_port_id, $this->autorate->destination_port_id])
                    ->where('quote_id', $this->quote->id);
                })->get(); 
            return $originCollection->concat($destinyCollection);
        }
        if ($this->quote->type == 'LCL') {
            $originCollection = ChargeLclAir::select('*')
                ->where('type_id', 1)
                ->whereHas('automatic_rate', function ($q) {
                    $q
                    ->whereIn('origin_port_id', [$this->autorate->origin_port_id, $this->autorate->destination_port_id])
                    ->where('quote_id', $this->quote->id);
                })->get();
            $destinyCollection = ChargeLclAir::select('*')
                ->where('type_id', 2)
                ->whereHas('automatic_rate', function ($q) {
                    $q
                    ->whereIn('destination_port_id', [$this->autorate->origin_port_id, $this->autorate->destination_port_id])
                    ->where('quote_id', $this->quote->id);
                })->get();
            return $originCollection->concat($destinyCollection);
        }
    }
}
