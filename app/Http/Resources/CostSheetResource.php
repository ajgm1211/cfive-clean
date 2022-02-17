<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\LocalChargeQuote;
use App\Charge;
use App\Http\Traits\QuoteV2Trait;

class CostSheetResource extends JsonResource
{
    use QuoteV2Trait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __construct($quote, $autorate)
    {   
        $this->quote = $quote;        
        $this->autorate = $autorate;
        $this->arrayContainers = $this->getContainerNames();
        $this->currencyToReport = $this->autorate->currency; //Obtener moneda en la que se quiere presentar el reporte
    }

    public function toArray($request)
    {   
        $dataFeatCostSheet = [];
        $buyingRates = [];
        $sellingRates = [];
        $buyingAmountAll = [];
        $sellingAmountAll = [];

        $dataFeatCostSheet['containers_head'] = $this->arrayContainers;    
        
        $dataRate = [];        

        // Obtenemos los local charges asociados a la cotización
        $local_charge_quotes_model = $this->getLocalChargesModel();
        
        $ratesBuying = [];    
        $ratesSelling = [];    
        $profit = [];
    
        $originRate = $this->autorate->origin_port_id;
        $destinoRate = $this->autorate->destination_port_id;

        $localesBuying = [];
        $localesSelling = [];
        
        $inlandsBuying = [];
        $inlandsSelling = [];

        // Obtener inlands
        $inlands = $this->getInlandsModel();

        $totalInland = [];

        foreach ($inlands as $inland) {

            if ($inland->port_id == $originRate || $inland->port_id == $destinoRate) {

                $ratesInland = json_decode($inland['rate']);    
                $markupInland = json_decode($inland['markup']);
                
                array_push($inlandsBuying, [
                    'type' => $inland->type,
                    'charge' => $inland->charge,
                    'currency' => ['currency_id' => $inland->currency_id, 'alphacode' => $inland->currency->alphacode], 
                    'rate' => $this->getAmountPerContainer($ratesInland)
                ]);

                $convertToCurrencyInlandRate = $this->convertToCurrencyQuote(
                    $local->currency, 
                    $this->currencyToReport, //Definir en que moneda deben expresarse los totales
                    $this->convertToArray($ratesInland), 
                    $this->quote
                );

                $convertToCurrencyInlandMarkup = $this->convertToCurrencyQuote(
                    $local->currency, 
                    $this->currencyToReport, //Definir en que moneda deben expresarse los totales
                    $this->convertToArray($markupInland), 
                    $this->quote
                );

                // acumular para calcular subtotal de compra
                array_push($buyingAmountAll, $this->getAmountPerContainer($convertToCurrencyInlandRate));

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
        }

        if (sizeof($totalInland) > 0) {
            array_push($inlandsSelling, [
                'totals' => $this->getAmountPerContainer($totalInland)
            ]);
            // acumular para calcular subtotal de venta
            array_push($sellingAmountAll, $this->getAmountPerContainer($totalInland));
        }        

        // Obtener charges de origen y su destino
        foreach ($local_charge_quotes_model as $local) {
            if ($local->port_id == $originRate || $local->port_id == $destinoRate) {
                array_push($localesBuying, [
                    'type' => $local->type->description,
                    'surcharge' => $local->charge,
                    'currency' => ['currency_id' => $local->currency_id, 'alphacode' => $local->currency->alphacode],
                    'amount' => $this->getAmountPerContainer($local['price'])
                ]);

                array_push($localesSelling, [
                    'type' => $local->type->description,
                    'surcharge' => $local->charge,
                    'currency' => ['currency_id' => $local->currency_id, 'alphacode' => $local->currency->alphacode],
                    'amount' => $this->getAmountPerContainer($local['total'])
                ]);
                
                $convertToCurrencylocalPrice = $this->convertToCurrencyQuote(
                    $local->currency, 
                    $this->currencyToReport, //Definir en que moneda deben expresarse los totales
                    $this->convertToArray($local['price']), 
                    $this->quote
                );

                $convertToCurrencylocalTotal = $this->convertToCurrencyQuote(
                    $local->currency, 
                    $this->currencyToReport, //Definir en que moneda deben expresarse los totales
                    $this->convertToArray($local['total']), 
                    $this->quote
                );

                // acumular para calcular subtotal de compra
                array_push($buyingAmountAll, $this->getAmountPerContainer($convertToCurrencylocalPrice));

                // acumular para calcular subtotal de venta
                array_push($sellingAmountAll, $this->getAmountPerContainer($convertToCurrencylocalTotal));

            }
        } 
        
        // agregar atributo carrier_name
        $this->autorate->carrier_name = $this->autorate->carrier->name;

        // Obtener valor y recargos de los fletes
        $freightChargesModel = $this->getChargesModel();

        $freightCharges = [];

        foreach ($freightChargesModel as $charge) {
            
            $freightAmountsCharges = json_decode($charge['amount']);   

            array_push($freightCharges, [
                'type' => $charge->type->description, 
                'surcharge' => $charge->surcharge->name, 
                'currency' => ['currency_id' => $charge->currency_id, 'alphacode' => $charge->currency->alphacode],
                'amount' => $this->getAmountPerContainer($freightAmountsCharges) // Se envía montos por contenedores en su moneda original
            ]);              
            
            // Acumular para calcular subtotal de compra. Se debe enviar montos por contenedor convertidos al tipo de moneda del rate            
            $convertToCurrencyfreightAmountsCharges = $this->convertToCurrencyQuote(
                $charge->currency , 
                $this->currencyToReport, //Definir en que moneda deben expresarse los totales
                $this->convertToArray($freightAmountsCharges), 
                $this->quote
            );            
            array_push($buyingAmountAll, $this->getAmountPerContainer($convertToCurrencyfreightAmountsCharges));              
        }
        
        // Obtener totales por flete (venta)
        $freightAmountSelling = json_decode($this->autorate->total);

        $convertToCurrencyfreightAmountSelling = $this->convertToCurrencyQuote(
            $this->autorate->currency, 
            $this->currencyToReport, //Definir en que moneda deben expresarse los totales
            $this->convertToArray($freightAmountSelling), 
            $this->quote
        );
        // acumular para calcular subtotal de venta
        array_push($sellingAmountAll, $this->getAmountPerContainer($freightAmountSelling));

        array_push($ratesBuying, [
            'freight' => $freightCharges,
            'locales' => $localesBuying,
            'inlands' => $inlandsBuying,
            'totals' => $this->sumaAmountPerContainer($buyingAmountAll)
        ]);

        array_push($ratesSelling, [
            'total_freight' => $this->getAmountPerContainer($freightAmountSelling),
            'locales' => $localesSelling,
            'inlands' => $inlandsSelling,
            'totals' => $this->sumaAmountPerContainer($sellingAmountAll)
        ]);

        $arrayProfit = $this->diffAmountPerContainer(
            $this->sumaAmountPerContainer($sellingAmountAll), 
            $this->sumaAmountPerContainer($buyingAmountAll)
        );

        $arrayPercentageProfit = $this->calculatePercentage(
            $arrayProfit, 
            $this->sumaAmountPerContainer($sellingAmountAll)
        );

        array_push($profit, [
            'profit' => $arrayProfit,
            'profit_percentage' => $arrayPercentageProfit
        ]);

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

        $dataFeatCostSheet['rates'] =  $dataRate;
        
        return $dataFeatCostSheet;
    }

    public function getContainerNames() {
        $equipments = $this->quote->getContainersFromEquipment($this->quote->equipment);
        $arrayContainers = [];
        foreach ($equipments as $equipment) {
            array_push($arrayContainers, ['name' => $equipment->name]);
        }
        return $arrayContainers;
    }

    public function getAmountPerContainer($array) {
        $amountPerContainer = [];    
        foreach ($this->arrayContainers as $container){ 
            $amount = "0.00";
            if($array){                
                foreach ($array as $clave => $val) {                     
                    if('c'.str_replace(' ', '', $container['name']) == $clave) {
                        $amount = $val;
                    }                     
                }       
            }                    
            array_push($amountPerContainer, ['name' => $container['name'], 'amount' => round((float)$amount, 2)]);              
        }
        return $amountPerContainer; 
    }

    public function sumaAmountPerContainer($array) {
        $total = $array[0];
        $cantContainers = sizeof($total);
        for ($i = 1; $i < sizeof($array); $i++) {
            for ($j = 0; $j < $cantContainers; $j++) {   
                $total[$j]['amount'] = round($total[$j]['amount'] + $array[$i][$j]['amount'], 2);                
            }   
        }
        return $total;
    }

    public function diffAmountPerContainer($arrayA, $arrayB) {
        $diff = $arrayA;
        $cantContainers = sizeof($arrayA);        
        for ($j = 0; $j < $cantContainers; $j++) {   
            $diff[$j]['amount'] = round($arrayA[$j]['amount'] - $arrayB[$j]['amount'], 2);                
        }
        return $diff;
    }

    public function calculatePercentage($arrayA, $arrayB) {        
        $percentage = $arrayA;
        $cantContainers = sizeof($arrayA);        
        for ($j = 0; $j < $cantContainers; $j++) {   
            $dec = $arrayA[$j]['amount'] / $arrayB[$j]['amount'];                
            $percentage[$j]['amount'] = $this->formatPercentage($dec);
        }
        return $percentage;
    }

    public function formatPercentage($num) {
        return number_format($num * 100, 2, ",", ".")." %";
    }

    public function getLocalChargesModel() {
        switch ($this->quote->type) {
            case 'FCL':
                $locales = LocalChargeQuote::where([
                    'quote_id' => $this->quote->id
                ])
                ->get();
                break;
            case 'LCL':
                $locales = LocalChargeQuoteLcl::where([
                    'quote_id' => $this->quote->id
                ])
                ->get();
                break;
        }
        return $locales;
    }

    public function getInlandsModel() {
        if ($this->quote->type == 'FCL') {
            $inlands = $this->quote->inland()->get();
        } else if ($this->quote->type == 'LCL') {
            $inlands = $this->quote->inland_lcl()->get();
        }
        return $inlands;
    }

    public function getChargesModel() {
        return Charge::where('type_id',3)
                ->filterByAutorate($this->autorate->id)
                ->get();        
    }

    public function convertToArray($data) {
        $amount_array = [];
        foreach($data as $key=>$value){
            $amount_array[$key] = $value;
        }
        return $amount_array;
    }
}
