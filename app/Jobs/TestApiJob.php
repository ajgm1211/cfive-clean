<?php

namespace App\Jobs;

use App\CalculationType;
use App\Carrier;
use App\CompanyUser;
use App\Container;
use App\ContainerCalculation;
use App\Contract;
use App\Currency;
use App\SurchargePerContract;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use App\Rate;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection as Collection;
use Illuminate\Http\Request;

class TestApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SearchTraitApi;
    use UtilTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //ini_set('memory_limit', '8000M');
        $rates = Contract::where('created_at', '>', '2022-03-01 00:00:00')->where('company_user_id',241)->with('rates')->get();
        $container_calculation = ContainerCalculation::get();
        foreach ($rates as $value) {

            $containers = Container::where('gp_container_id', $value->gp_container_id)->get();
            $surcharges = \DB::select(\DB::raw('call proc_localchar(' . $value->id . ')'));

            foreach ($value->rates as $data) {

                $resultado['contract']['surcharges'] = array();

                if ($surcharges != null) {
                    for ($i = 0; $i < count($surcharges); $i++) {

                        $montosLocal = array();
                        $montosLocal2 = array();

                        $calculationID = CalculationType::where('name', $surcharges[$i]->calculation_type)->first();
                        $currencyID = Currency::where('alphacode', $surcharges[$i]->currency)->first();

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
                                    $$name_rate = 0;
                                }
                            } else {
                                $rateMount = $data->{$options->field_rate};
                                $$name_rate = $rateMount;
                            }

                            if (in_array($calculationID->id, $$var)) {
                                $monto = $this->perTeu($surcharges[$i]->ammount, $calculationID->id, $cont->code);
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

                        SurchargePerContract::create([
                            'charge' => $surcharges[$i]->surcharge,
                            'type' => $surcharges[$i]->changetype,
                            'calculation_type' => $surcharges[$i]->calculation_type,
                            'currency' => $surcharges[$i]->currency,
                            'contract_id' => $value->id,
                            'rates' => json_encode($montosLocal),
                            'origin_port' => $surcharges[$i]->port_orig,
                            'destination_port' => $surcharges[$i]->port_dest,
                        ]);
                    }
                }
            }
        }
    }
}
