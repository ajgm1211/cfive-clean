<?php

namespace App\Http\Resources;

use App\CalculationType;
use App\CompanyUser;
use App\Container;
use App\ContainerCalculation;
use App\Currency;
use App\Http\Traits\UtilTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class GetContractApiResource extends JsonResource
{

    use UtilTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //Fetching containers and calculations from DB
        $containers = Container::where('gp_container_id', $this->contract->gp_container_id)->get();
        $container_calculation = ContainerCalculation::get();
 
        $data = [
            //General data related to the contract
            'contract' => [
                'id' => $this->contract->id,
                'reference' => $this->contract->name,
                'code' => $this->contract->contract_code,
                'carrier' => $this->carrier->name,
                'carrier_scac' => $this->carrier->scac,
                'direction' => $this->contract->direction->name,
                'origin' => ucwords(strtoupper($this->port_origin->display_name)),
                'destination' => ucwords(strtoupper($this->port_destiny->display_name)),
                'valid_from' => $this->contract->validity,
                'valid_until' => $this->contract->expire,
            ],
            //Rates
            'ocean_freight' => [
                $this->oceanFreight($this, $containers, $container_calculation),
            ],
            'surcharges' => [
                $this->surcharges($this, $containers, $container_calculation),
            ],
            //Total (Ocean Freight + Surcharges)
            'all_in' => [
                $this->allIn($this, $containers, $container_calculation),
            ],
        ];

        return $data;
    }

    /**
     * 
     * Creating array for ocean freight's amounts
     * 
     * @param mixed $data
     * @param mixed $containers
     * @param mixed $container_calculation
     * 
     * @return array
     */
    public function oceanFreight($data, $containers, $container_calculation)
    {

        $amount = array();
        $amount2 = array();

        foreach ($containers as $cont) {

            $name_rate = 'rate' . $cont->code;

            $var = 'array' . $cont->code;
            $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();

            $options = json_decode($cont->options);
            if (@$options->field_rate == 'containers') {
                $jsonContainer = json_decode($data->{$options->field_rate});
                if (isset($jsonContainer->{'C' . $cont->code})) {
                    $rateMount = $jsonContainer->{'C' . $cont->code};
                    $$name_rate = $rateMount;
                } else {
                    $rateMount = 0;
                    $$name_rate = $rateMount;
                }
            } else {
                $rateMount = $data->{$options->field_rate};
                $$name_rate = $rateMount;
            }

            $amount2 = array($cont->code => (float)$rateMount);
            $amount = array_merge($amount, $amount2);
        }

        $arraySecondPartAmount = array(
            'charge' => 'Ocean Freight',
            'currency' => $data->currency->alphacode,

        );
        $ocean_freight = array_merge($amount, $arraySecondPartAmount);

        return $ocean_freight;
    }

    /**
     * 
     * Creating array for surcharges' amounts
     * 
     * @param mixed $data
     * @param mixed $containers
     * @param mixed $container_calculation
     * 
     * @return array
     */
    public function surcharges($data, $containers, $container_calculation)
    {
        //Getting localcharges from DB
        $data1 = \DB::select(\DB::raw('call proc_localchar(' . $data->contract->id . ')'));

        $result = array();
        $count = count($data1);
        if ($data1 != null) {
            for ($i = 0; $i < $count; $i++) {
                if (
                    $data1[$i]->deleted_at == null && (strpos($data1[$i]->carrier, $data->carrier->name) !== false) && ((strpos($data1[$i]->port_orig, $data->port_origin->code) !== false) && (strpos($data1[$i]->port_dest, $data->port_destiny->code) !== false) || ($data1[$i]->port_orig == "ALL" && (strpos($data1[$i]->port_dest, $data->port_destiny->code) !== false)) || ((strpos($data1[$i]->port_orig, $data->port_origin->code) !== false) && $data1[$i]->port_dest == "ALL") || ($data1[$i]->port_orig == "ALL" && $data1[$i]->port_dest == "ALL"))
                ) {
                    $amountLocal = array();
                    $amountLocal2 = array();
                    $arrayFirstPartLocal = array(
                        'charge' => $data1[$i]->surcharge,
                        'type' => $data1[$i]->changetype,
                        'calculation_type' => $data1[$i]->calculation_type,
                        'currency' => $data1[$i]->currency,
                    );

                    $calculationID = CalculationType::select('id')->where('name', $data1[$i]->calculation_type)->first();

                    foreach ($containers as $cont) {
                        $name_arreglo = 'array' . $cont->code;
                        $$name_arreglo = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();

                        if (in_array($calculationID->id, $$name_arreglo)) {
                            $amount = $this->perTeu($data1[$i]->ammount, $calculationID->id, $cont->code);
                            $amountLocal2 = array($cont->code => (float)$amount);
                        } else {
                            $amountLocal2 = array($cont->code => '0');
                        }
                        $amountLocal = array_merge($amountLocal, $amountLocal2);
                    }
                    $arrayFirstPartLocal = array_merge($arrayFirstPartLocal, $amountLocal);

                    $result[] = $arrayFirstPartLocal;
                }
            }
        }

        return $result;
    }

    /**
     * 
     * Creating array for all in amounts
     * 
     * @param mixed $data
     * @param mixed $containers
     * @param mixed $container_calculation
     * 
     * @return array
     */
    public function allIn($data, $containers, $container_calculation)
    {
        $ocean_freight = $this->oceanFreight($data, $containers, $container_calculation);
        $surcharges = $this->surcharges($data, $containers, $container_calculation);
        $all_in = [];

        foreach ($ocean_freight as $key => $value) {
            foreach ($containers as $cont) {
                if ($cont->code == $key) {
                    $all_in[$key] = $value;
                }
            }
        }

        foreach ($surcharges as $key => $value) {
            foreach ($value as $v => $surcharge) {
                foreach ($all_in as $k => $all) {
                    if ($k == $v) {
                        $currencyID = Currency::select('id')->where('alphacode', $value['currency'])->first();
                        $currency_rate = $this->ratesCurrency($currencyID->id, $data->currency->alphacode);
                        $amount = number_format(($surcharge / $currency_rate), 2, '.', '');
                        $all_in[$k] += (float)$amount;
                    }
                }
            }
        }

        $all_in['charge'] = 'All In';
        $all_in['currency'] = $data->currency->alphacode;

        return $all_in;
    }

    /**
     * @param mixed $amount
     * @param mixed $calculation_type
     * @param mixed $code
     * 
     * @return void
     */
    public function perTeu($amount, $calculation_type, $code)
    {
        $arrayTeu = CalculationType::where('options->isteu', true)->pluck('id')->toArray();
        $codeArray = Container::where('code', 'like', '20%')->pluck('code')->toArray();

        if (!in_array($code, $codeArray)) {
            if (in_array($calculation_type, $arrayTeu)) {
                $amount = $amount * 2;
                return $amount;
            } else {
                return $amount;
            }
        } else {
            return $amount;
        }
    }
}
