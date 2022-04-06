<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetContractApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        foreach($this->rates as $rate){
            $data = [
                'general' => [
                    'contract' => $rate->contract->name,
                    'reference' => $rate->contract->id,
                    'carrier' => $rate->carrier->name,
                    'direction' => $rate->contract->direction->name,
                    'origin' => ucwords(strtoupper($rate->port_origin->code)),
                    'destination' => ucwords(strtoupper($rate->port_destiny->code)),
                    'valid_from' => $rate->contract->validity,
                    'valid_until' => $rate->contract->expire,
                ]
            ];
        }

        /*foreach ($arreglo as $data) {

            $montos = array();
            $montos2 = array();
            $montosAllIn = array();
            $montosAllInTot = array();
       
            foreach ($containers as $cont) {
                $name_rate = 'rate' . $cont->code;

                $var = 'array' . $cont->code;
                $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();
       
                $options = json_decode($cont->options);
                if (@$options->field_rate == 'containers') {
                    $test = json_encode($data->{$options->field_rate});
                    $jsonContainer = json_decode($data->{$options->field_rate});
                    if (isset($jsonContainer->{'C' . $cont->code})) {
                        $rateMount = $jsonContainer->{'C' . $cont->code};
                        $$name_rate = $rateMount;
                        $montosAllIn = array($cont->code => (float)$$name_rate);
                    } else {
                        $rateMount = 0;
                        $$name_rate = $rateMount;
                        $montosAllIn = array($cont->code => (float)$$name_rate);
                    }
                } else {
                    $rateMount = $data->{$options->field_rate};
                    $$name_rate = $rateMount;
                    $montosAllIn = array($cont->code => (float)$$name_rate);
                }

                $montos2 = array($cont->code => (float)$rateMount);
                $montos = array_merge($montos, $montos2);
                $montosAllInTot = array_merge($montosAllInTot, $montosAllIn);

            }
            $arrayFirstPartAmount = array(
                'contract' => $data->contract->name,
                'reference' => $data->contract->id,
                'carrier' => $data->carrier->name,
                'direction' => $data->contract->direction->name,
                'origin' => ucwords(strtoupper($data->port_origin->code)),
                'destination' => ucwords(strtoupper($data->port_destiny->code)),
                'valid_from' => $data->contract->validity,
                'valid_until' => $data->contract->expire,
            );
            $arraySecondPartAmount = array(
                'charge' => 'freight',
                'currency' => $data->currency->alphacode,

            );
            $ocean_freight = array_merge($montos, $arraySecondPartAmount);
            $resultado['contract']['general'] = $arrayFirstPartAmount;
            $resultado['contract']['ocean_freight'] = $ocean_freight;

            $a++;
            // Local charges
            if ($contractId != $data->contract->id) {

                $contractId = $data->contract->id;
                $data1 = \DB::select(\DB::raw('call proc_localchar(' . $data->contract->id . ')'));
                $arrayCompleteLocal = array();
                $resultado['contract']['surcharges'] = array();
                if ($data1 != null) {
                    for ($i = 0; $i < count($data1); $i++) {
                        $montosLocal = array();
                        $montosLocal2 = array();
                        $arrayFirstPartLocal = array(
                            'charge' => $data1[$i]->surcharge,
                            'type' => $data1[$i]->changetype,
                            'calculation_type' => $data1[$i]->calculation_type,

                        );

                        $calculationID = CalculationType::where('name', $data1[$i]->calculation_type)->first();
                        $currencyID = Currency::where('alphacode', $data1[$i]->currency)->first();
                        
                        foreach ($containers as $cont) {
                            $name_arreglo = 'array' . $cont->code;
                            $name_rate = 'rate' . $cont->code;
                            if (in_array($calculationID->id, $$name_arreglo)) {
                                $monto = $this->perTeu($data1[$i]->ammount, $calculationID->id, $cont->code);
                                $currency_rate = $this->ratesCurrency($currencyID->id, $data->currency->alphacode);
                                $$name_rate = number_format($$name_rate + ($monto / $currency_rate), 2, '.', '');
                                $montosAllInTot[$cont->code] = (float)$$name_rate;
                                $montosLocal2 = array($cont->code => (float)$monto);
                                $montosLocal = array_merge($montosLocal, $montosLocal2);
                            } else {
                                $montosLocal2 = array($cont->code => '0');

                                $montosLocal = array_merge($montosLocal, $montosLocal2);
                            }
                        }
                        $arrayFirstPartLocal = array_merge($arrayFirstPartLocal, $montosLocal);

                        $arraySecondPartLocal = array(
                            'currency' => $data1[$i]->currency,

                        );
                        $resultado['contract']['surcharges'][] = array_merge($arrayFirstPartLocal, $arraySecondPartLocal);
                    }
                }
            }
        }*/

        return $data;
    }
}
