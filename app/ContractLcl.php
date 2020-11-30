<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use App\CompanyUser;
use App\ScheduleType;
use App\Currency;
use App\RateLcl;
use App\LocalChargeLcl;
use App\GlobalChargeLcl;
use App\User;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;

class ContractLcl extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use SearchTraitApi;
    use UtilTrait;
    use \OwenIt\Auditing\Auditable;
    protected $table    = "contracts_lcl";
    protected $fillable = ['id', 'name', 'number', 'company_user_id', 'direction_id', 'account_id', 'validity', 'expire', 'status', 'code', 'is_manual'];

    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function rates()
    {
        return $this->hasMany('App\RateLcl', 'contractlcl_id');
    }

    public function localcharges()
    {
        //return $this->hasManyThrough('App\LocalCharCarrier', 'App\LocalCharge');
        return $this->hasMany('App\LocalChargeLcl', 'contractlcl_id');
    }

    public function contract_company_restriction()
    {

        return $this->HasMany('App\ContractLclCompanyRestriction', 'contractlcl_id');
    }

    public function contract_user_restriction()
    {

        return $this->HasMany('App\ContractLclUserRestriction', 'contractlcl_id');
    }

    public function carriers()
    {
        return $this->hasMany('App\ContractCarrierLcl', 'contract_id');
    }

    public function direction()
    {
        return $this->belongsTo('App\Direction', 'direction_id');
    }

    /**
     * ContractCarrierSync
     *
     * @param  mixed $carriers
     * @param  mixed $api
     * @return void
     */
    public function ContractCarrierSync($carriers, $api = false)
    {

        DB::table('contracts_carriers_lcl')->where('contract_id', '=', $this->id)->delete();

        if ($api) {
            $carriers = explode(",", $carriers);
        }

        foreach ($carriers as $carrier) {
            ContractCarrierLcl::create([
                'carrier_id'    => $carrier,
                'contract_id'   => $this->id
            ]);
        }
    }

    /**
     * Store file in storage
     *
     * @param  blob  $file
     * @return void
     */
    public function StoreInMedia($file, $name)
    {
        \Storage::disk('LclRequest')->put($name, \File::get($file));
        /*$this->addMedia($file)->addCustomHeaders([
            'ACL' => 'public-read'
        ])->toMediaCollection('document', 'LclRequest');*/
    }

    /**
     * processSearchByIdLcl
     *
     * @return json
     */
    public function processSearchByIdLcl($response = false, $convert = false)
    {

        //Variables del usuario conectado
        $company_user_id = Auth::user()->company_user_id;

        //Variables para cargar el  Formulario
        $chargesOrigin = 'true';
        $chargesDestination = 'true';
        $chargesFreight = 'true';
        $total_quantity = 1;
        $chargeable_weight = 1;
        $total_weight = 1;

        $company_user = User::where('id', Auth::id())->first();
        $company_setting = CompanyUser::where('id', Auth::user()->company_user_id)->first();
        $typeCurrency = 'USD';
        $idCurrency = 149;

        if ($company_setting->currency_id != null) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
            $idCurrency = $company_setting->currency_id;
        }

        $weight = $chargeable_weight;
        $weight = number_format($weight, 2, '.', '');
        $arregloNull = array();
        $arregloNull = json_encode($arregloNull);
        $freighPercentage = 0;
        $freighAmmount = 0;
        $localPercentage = 0;
        $localAmmount = 0;
        $freighMarkup = 0;
        $localMarkup = 0;

        // Traer cantidad total de paquetes y pallet segun sea el caso
        $package_pallet = $this->totalPalletPackage($total_quantity);

        //Colecciones

        $general = new Collection();
        $collectionRate = new Collection();

        // Rates LCL

        $rates = RateLcl::with(['carrier' => function ($query) {
            $query->select('id', 'name', 'uncode', 'image', 'image as url');
        }])->with(['port_origin' => function ($query) {
            $query->select('id', 'display_name', 'country_id', 'name', 'code');
        }])->with(['port_destiny' => function ($query) {
            $query->select('id', 'display_name', 'country_id', 'name', 'code');
        }])->whereHas('contract', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id)->where('status', 'publish')->where('name', $this->name);
        })->get();

        //Guard if
        if (count($rates) == 0) {
            return response()->json(['message' => 'No freight rates were found for this trade route', 'state' => 'CONVERSION_PENDING'], 200);
        }

        foreach ($rates as $data) {

            if ($convert) {
                $typeCurrency = $company_setting->currency->alphacode;
            } else {
                $typeCurrency =  $data->currency->alphacode;
            }

            $markupFreightCurre = $typeCurrency;
            $markupLocalCurre = $typeCurrency;
            $totalFreight = 0;
            $FreightCharges = 0;
            $totalRates = 0;
            $totalOrigin = 0;
            $totalDestiny = 0;
            $totalQuote = 0;
            $totalAmmount = 0;
            $collectionOrig = new Collection();
            $collectionDest = new Collection();
            $collectionFreight = new Collection();
            $collectionRate = new Collection();
            $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);
            $array_ocean_freight = array();
            $totalChargeOrig = 0;
            $totalChargeDest = 0;

            if ($total_weight != null) {

                $subtotalT = $weight * $data->uom;
                $totalT = ($weight * $data->uom) / $rateC;
                $priceRate = $data->uom;

                if ($subtotalT < $data->minimum) {
                    $subtotalT = $data->minimum;
                    $totalT = $subtotalT / $rateC;
                    $priceRate = $data->minimum / $weight;
                    $priceRate = number_format($priceRate, 2, '.', '');
                }

                // MARKUPS
                if ($freighPercentage != 0) {
                    $freighPercentage = intval($freighPercentage);
                    $markup = ($totalT * $freighPercentage) / 100;
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $markup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)");
                } else {

                    $markup = trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $freighMarkup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre);
                }

                $totalT = number_format($totalT, 2, '.', '');
                $totalFreight += $totalT;
                $totalRates += $totalT;

                $array_ocean_freight = array('type' => 'Ocean Freight', 'quantity' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'total' => $subtotalT, 'currency' => $data->currency->alphacode);
                $array = array('type' => 'Ocean Freight', 'quantity' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode, 'subtotal' => $subtotalT, 'total' => $totalT . " " . $typeCurrency, 'currency_id' => $data->currency_id);
                $array = array_merge($array, $arraymarkupT);
                $collectionRate->push($array);
                $data->setAttribute('montF', $array);
            }
            // POR PAQUETE
            /*if ($request->input('total_weight_pkg') != null) {

                $subtotalT = $weight * $data->uom;
                $totalT = ($weight * $data->uom) / $rateC;
                $priceRate = $data->uom;

                if ($subtotalT < $data->minimum) {
                    $subtotalT = $data->minimum;
                    $totalT = $subtotalT / $rateC;
                    $priceRate = $data->minimum / $weight;
                    $priceRate = number_format($priceRate, 2, '.', '');
                }
                // MARKUPS
                if ($freighPercentage != 0) {
                    $freighPercentage = intval($freighPercentage);
                    $markup = ($totalT * $freighPercentage) / 100;
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $markup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)");
                } else {

                    $markup = trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $freighMarkup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre);
                }

                $totalT = number_format($totalT, 2, '.', '');
                $totalFreight += $totalT;
                $totalRates += $totalT;
                $array_ocean_freight = array('type' => 'Ocean Freight', 'quantity' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'total' => $subtotalT, 'currency' => $data->currency->alphacode);
                $array = array('type' => 'Ocean Freight', 'cantidad' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode, 'subtotal' => $subtotalT, 'total' => $totalT . " " . $typeCurrency, 'idCurrency' => $data->currency_id);
                $array = array_merge($array, $arraymarkupT);
                $collectionRate->push($array);
                $data->setAttribute('montF', $array);
            }*/

            $data->setAttribute('rates', $collectionRate);

            $orig_port = array($data->origin_port);
            $dest_port = array($data->destiny_port);

            $carrier[] = $data->carrier_id;

            // id de los carrier ALL
            $carrier_all = 26;
            array_push($carrier, $carrier_all);

            //Calculation type
            $arrayBlHblShip = array('1', '2', '3', '16'); // id  calculation type 1 = HBL , 2=  Shipment , 3 = BL , 16 per set
            $arraytonM3 = array('4', '11', '17'); //  calculation type 4 = Per ton/m3
            $arraytonCompli = array('6', '7', '12', '13'); //  calculation type 4 = Per ton/m3
            $arrayPerTon = array('5', '10'); //  calculation type 5 = Per  TON
            $arrayPerKG = array('9'); //  calculation type 5 = Per  TON
            $arrayPerPack = array('14'); //  per package
            $arrayPerPallet = array('15'); //  per pallet

            // Local charges
            $localChar = LocalChargeLcl::where('contractlcl_id', '=', $data->contractlcl_id)->whereHas('localcharcarrierslcl', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->with('localcharportslcl.portOrig', 'localcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

            foreach ($localChar as $local) {

                $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);
                //Totales peso y volumen
                if ($total_weight != null) {
                    $totalW = $total_weight / 1000;
                    $totalV = 1;
                }

                // Condicion para enviar los terminos de venta o compra
                if (isset($local->surcharge->saleterm->name)) {
                    $terminos = $local->surcharge->saleterm->name;
                } else {
                    $terminos = $local->surcharge->name;
                }

                if (in_array($local->calculationtypelcl_id, $arrayBlHblShip)) {
                    $cantidadT = 1;
                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                           /* if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;

                                    // MARKUP
                                    //$markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => "-", 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_type_name' => $local->calculationtypelcl->name, 'type' => 'origin');

                                    $collectionOrig->push($arregloOrig);
                                }
                            }
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;
                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => "-", 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_type_name' => $local->calculationtypelcl->name, 'type' => 'destination');

                                    $collectionDest->push($arregloDest);
                                }
                            }*/
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => "-", 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_type_name' => $local->calculationtypelcl->name, 'type' => 'freight');

                                    $collectionFreight->push($arregloPC);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arraytonM3)) {

                    //ROUNDED

                    if ($local->calculationtypelcl_id == '11') {
                        $ton_weight = ceil($weight);
                    } else {
                        $ton_weight = $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            /*if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTonM3 = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $cantidadT, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigTonM3 = array_merge($arregloOrigTonM3, $markupTonM3);

                                    $collectionOrig->push($arregloOrigTonM3);
                                }
                            }
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $cantidadT, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupTonM3);

                                    $collectionDest->push($arregloDest);
                                }
                            }*/
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'cantidad' => $cantidadT, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'idCurrency' => $local->currency->id, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloPC = array_merge($arregloPC, $markupTonM3);

                                    $collectionFreight->push($arregloPC);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerTon)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            //ROUNDED
                            if ($local->calculationtypelcl_id == '10') {
                                $totalW = ceil($totalW);
                            }

                            /*if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTon = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigTon = array_merge($arregloOrigTon, $markupTON);
                                    $collectionOrig->push($arregloOrigTon);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupTON);

                                    $collectionDest->push($arregloDest);
                                }
                            }*/
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $mont, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloPC = array_merge($arregloPC, $markupTON);

                                    $collectionFreight->push($arregloPC);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arraytonCompli)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                           /* if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {

                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }

                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    //$totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrig = array_merge($arregloOrig, $markupTONM3);
                                    $dataOrig[] = $arregloOrig;
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupTONM3);
                                    $dataDest[] = $arregloDest;
                                }
                            }*/

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            if ($totalW < 1) {
                                                $mont = $local->minimum * $totalW;
                                            } else {
                                                $mont = $local->minimum / $totalW;
                                            }
                                        }
                                    }
                                    // Markup
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloPC = array_merge($arregloPC, $markupTONM3);
                                    $dataFreight[] = $arregloPC;
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerKG)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            /*if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalW * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $totalW;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigKg = array_merge($arregloOrigKg, $markupKG);
                                    $collectionOrig->push($arregloOrigKg);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalW * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $totalW;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDestKg = array_merge($arregloDestKg, $markupKG);

                                    $collectionDest->push($arregloDestKg);
                                }
                            }*/

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalW * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $totalW;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $mont, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreightKg = array_merge($arregloFreightKg, $markupKG);

                                    $collectionFreight->push($arregloFreightKg);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerPack)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $package_cantidad = $package_pallet['package']['cantidad'];
                           /* if ($chargesOrigin != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $package_cantidad;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpack = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigpack = array_merge($arregloOrigpack, $markupKG);
                                    $collectionOrig->push($arregloOrigpack);
                                }
                            }

                            if ($chargesDestination != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDestPack = array_merge($arregloDestPack, $markupKG);

                                    $collectionDest->push($arregloDestPack);
                                }
                            }*/

                            if ($chargesFreight != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $mont, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreightPack = array_merge($arregloFreightPack, $markupKG);

                                    $collectionFreight->push($arregloFreightPack);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerPallet)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $pallet_cantidad = $package_pallet['pallet']['cantidad'];
                          /*  if ($chargesOrigin != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
                                    $collectionOrig->push($arregloOrigpallet);
                                }
                            }

                            if ($chargesDestination != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

                                    $collectionDest->push($arregloDestPallet);
                                }
                            }*/

                            if ($chargesFreight != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $mont, 'currency' => $local->currency->alphacode,  'calculation_name' => $local->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);

                                    $collectionFreight->push($arregloFreightPallet);
                                }
                            }
                        }
                    }
                }
            }  // Fin del calculo de los local charges

            //############ Global Charges   ####################

            /*$globalChar = GlobalChargeLcl::whereHas('globalcharcarrierslcl', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                $query->whereHas('globalcharportlcl', function ($q) use ($orig_port, $dest_port) {
                    $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                })->orwhereHas('globalcharcountrylcl', function ($q) use ($origin_country, $destiny_country) {
                    $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                });
            })->where('company_user_id', '=', $company_user_id)->with('globalcharportlcl.portOrig', 'globalcharportlcl.portDest', 'globalcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

            foreach ($globalChar as $global) {
                $rateMountG = $this->ratesCurrency($global->currency->id, $typeCurrency);
                if ($total_weight != null) {
                    $totalW = $total_weight / 1000;
                    $totalV = 1;
                    $totalWeight = $total_weight;
                }

                // Condicion para enviar los terminos de venta o compra
                if (isset($global->surcharge->saleterm->name)) {
                    $terminos = $global->surcharge->saleterm->name;
                } else {
                    $terminos = $global->surcharge->name;
                }

                if (in_array($global->calculationtypelcl_id, $arrayBlHblShip)) {
                    $cantidadT = 1;
                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;

                                    // MARKUP

                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => '-', 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrig = array_merge($arregloOrig, $markupBL);
                                    //$origGlo["origin"] = $arregloOrig;
                                    $collectionOrig->push($arregloOrig);
                                    // $collectionGloOrig->push($arregloOrig);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;
                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => '1', 'price' => $global->ammount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupBL);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => '-', 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreight = array_merge($arregloFreight, $markupBL);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arraytonM3)) {
                    //ROUNDED
                    if ($global->calculationtypelcl_id == '11') {
                        $ton_weight = ceil($weight);
                    } else {
                        $ton_weight = $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $cantidadT, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrig = array_merge($arregloOrig, $markupTonM3);

                                    $collectionOrig->push($arregloOrig);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $cantidadT, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupTonM3);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }
                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $cantidadT, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreight = array_merge($arregloFreight, $markupTonM3);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerTon)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            //ROUNDED
                            if ($global->calculationtypelcl_id == '10') {
                                $totalW = ceil($totalW);
                            }
                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrig = array_merge($arregloOrig, $markupTON);

                                    $collectionOrig->push($arregloOrig);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupTON);
                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;

                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreight = array_merge($arregloFreight, $markupTON);
                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arraytonCompli)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }

                                    // MARKUP
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrig = array_merge($arregloOrig, $markupTONM3);
                                    $dataGOrig[] = $arregloOrig;
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {
                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV; // monto por unidad
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDest = array_merge($arregloDest, $markupTONM3);
                                    $dataGDest[] = $arregloDest;
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP

                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreight = array_merge($arregloFreight, $markupTONM3);
                                    $dataGFreight[] = $arregloFreight;
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerKG)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigKg = array_merge($arregloOrigKg, $markupKG);

                                    $collectionOrig->push($arregloOrigKg);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDestKg = array_merge($arregloDestKg, $markupKG);
                                    $collectionDest->push($arregloDestKg);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreightKg = array_merge($arregloFreightKg, $markupKG);
                                    $collectionFreight->push($arregloFreightKg);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerPack)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $package_cantidad = $package_pallet['package']['cantidad'];
                            if ($chargesOrigin != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigPack = array_merge($arregloOrigPack, $markupKG);

                                    $collectionOrig->push($arregloOrigPack);
                                }
                            }

                            if ($chargesDestination != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDestPack = array_merge($arregloDestPack, $markupKG);
                                    $collectionDest->push($arregloDestPack);
                                }
                            }

                            if ($chargesFreight != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreightPack = array_merge($arregloFreightPack, $markupKG);
                                    $collectionFreight->push($arregloFreightPack);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerPallet)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $pallet_cantidad = $package_pallet['pallet']['cantidad'];

                            if ($chargesOrigin != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'origin');
                                    //$arregloOrigPallet = array_merge($arregloOrigPallet, $markupKG);

                                    $collectionOrig->push($arregloOrigPallet);
                                }
                            }

                            if ($chargesDestination != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'destination');
                                    //$arregloDestPallet = array_merge($arregloDestPallet, $markupKG);
                                    $collectionDest->push($arregloDestPallet);
                                }
                            }

                            if ($chargesFreight != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'surcharge_options' => json_decode($local->surcharge->options), 'quantity' => $unidades, 'price' => $totalAmmount, 'currency' => $global->currency->alphacode,  'calculation_name' => $global->calculationtypelcl->name, 'type' => 'freight');
                                    //$arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);
                                    $collectionFreight->push($arregloFreightPallet);
                                }
                            }
                        }
                    }
                }
            }*/

            //############ Fin Global Charges ##################

            // Locales

            if (!empty($dataOrig)) {
                $collectOrig = Collection::make($dataOrig);

                $m3tonOrig = $collectOrig->groupBy('surcharge_name')->map(function ($item) use ($collectionOrig, &$totalOrigin, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();

                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalOrigin += $totalA[0];
                        $collectionOrig->push($test);

                        return $test;
                    }
                });
            }

            if (!empty($dataDest)) {
                $collectDest = Collection::make($dataDest);
                $m3tonDest = $collectDest->groupBy('surcharge_name')->map(function ($item) use ($collectionDest, &$totalDestiny, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalDestiny += $totalA[0];
                        //            $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataFreight)) {

                $collectFreight = Collection::make($dataFreight);
                $m3tonFreight = $collectFreight->groupBy('surcharge_name')->map(function ($item) use ($collectionFreight, &$totalFreight, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalFreight += $totalA[0];
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
                        return $test;
                    }
                });
            }

            // Globales
            if (!empty($dataGOrig)) {
                $collectGOrig = Collection::make($dataGOrig);

                $m3tonGOrig = $collectGOrig->groupBy('surcharge_name')->map(function ($item) use ($collectionOrig, &$totalOrigin, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalOrigin += $totalA[0];

                        //$arre['origin'] = $test;
                        $collectionOrig->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataGDest)) {
                $collectGDest = Collection::make($dataGDest);
                $m3tonDestG = $collectGDest->groupBy('surcharge_name')->map(function ($item) use ($collectionDest, &$totalDestiny, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalDestiny += $totalA[0];
                        // $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataGFreight)) {

                $collectGFreight = Collection::make($dataGFreight);
                $m3tonFreightG = $collectGFreight->groupBy('surcharge_name')->map(function ($item) use ($collectionFreight, &$totalFreight, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalFreight += $totalA[0];
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
                        return $test;
                    }
                });
            }

            //#######################################################################
            //Formato subtotales y operacion total quote
            $totalChargeOrig += $totalOrigin;
            $totalChargeDest += $totalDestiny;
            $totalFreight = number_format($totalFreight, 2, '.', '');
            $FreightCharges = number_format($FreightCharges, 2, '.', '');
            $totalOrigin = number_format($totalOrigin, 2, '.', '');
            $totalDestiny = number_format($totalDestiny, 2, '.', '');
            $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
            $totalQuoteSin = number_format($totalQuote, 2, ',', '');
            $totales = array('freight' => $totalFreight );

            $transit_time = $this->transitTime($data->port_origin->id, $data->port_destiny->id, $data->carrier->id, $data->contract->status);

            $data->setAttribute('via', $transit_time['via']);
            $data->setAttribute('transit_time', $transit_time['transit_time']);
            $data->setAttribute('service', $transit_time['service']);
            $data->setAttribute('sheduleType', null);

            $routes = array();
            $routes['Rates']['type'] = 'LCL';
            $routes['Rates']['origin_port'] = array('name' => $data->port_origin->name, 'code' => $data->port_origin->code);
            $routes['Rates']['destination_port'] = array('name' => $data->port_destiny->name, 'code' => $data->port_destiny->code);

            //Ocean Freight
            $routes['Rates']['ocean_freight'] = $array_ocean_freight;

            //Local Charges
            if (!empty($collectionOrig)) {
                $routes['Rates']['origin_charges'] = $collectionOrig;
            }

            if (!empty($collectionDest)) {
                $routes['Rates']['destination_charges'] = $collectionDest;
            }

            if (!empty($collectionFreight)) {
                $routes['Rates']['freight_charges'] = $collectionFreight;
            }

            $routes['Rates']['total'] = $totalQuote;
            $routes['Rates']['currency'] = $typeCurrency;

            $detail = $this->compactResponse($routes, $data, $typeCurrency, $totalQuoteSin, $response,$totales);

            $general->push($detail);
        }

        return response()->json($general);
    }

    public function compactResponse($routes, $data, $currency, $totalQuote, $response,$totales)
    {

        switch ($response) {
            case 'compact':
                $detalle = array($data->port_origin->code, $data->port_destiny->code, $data->via, (int) $totales['freight'], (float) $data->uom, $currency, $data->transit_time, $data->contract->comments);
                break;
            default:
                $detalle = $routes;

                //Schedules
                $detalle['Rates']['schedule']['transit_time'] = $data->transit_time;
                $detalle['Rates']['schedule']['via'] = $data->via;

                //set carrier logo url
                $detalle['Rates']['carrier'] = $data->carrier;
                $detalle['Rates']['contract']['valid_from'] = $data->contract->validity;
                $detalle['Rates']['contract']['valid_until'] =   $data->contract->expire;
                $detalle['Rates']['contract']['number'] =   $data->contract->number;
                $detalle['Rates']['contract']['ref'] =   $data->contract->name;
                $detalle['Rates']['contract']['status'] =   $data->contract->status == 'publish' ? 'published' : $data->contract->status;
                $detalle['Rates']['remarks'] = $data->contract->remarks;
                break;
        }

        return $detalle;
    }
}
