<?php 



public function processSearchLCL(Request $request, $code_origin, $code_destination)
{

    //Variables del usuario conectado
    $company_user_id = \Auth::user()->company_user_id;
    $user_id = \Auth::id();
    $general = array();

    //Variables para cargar el  Formulario
    $chargesOrigin = true;
    $chargesDestination = true;
    $chargesFreight = true;

    // Request Formulario
    $portOrig = Harbor::where('code', $code_origin)->firstOrFail();
    $portDest = Harbor::where('code', $code_destination)->firstOrFail();

    $origin_port[] = $portOrig->id;
    $origin_country[] = $portOrig->country_id;

    $destiny_port[] = $portDest->id;
    $destiny_country[] = $portDest->country_id;

    $total_weight = $request->input('total_weight');
    $total_volume = $request->input('total_volume');
    $company_id = ($request->input('companyID') != null) ? $request->input('companyID') : null;

    //  $mode = $request->mode;
    $dateRange = $request->input('date');
    $dateRange = explode("/", $dateRange);
    $dateSince = $dateRange[0];
    $dateUntil = $dateRange[1];

    $total_weight = $total_weight / 1000;
    if ($total_volume > $total_weight) {
        $chargeable_weight = $total_volume;
    } else {
        $chargeable_weight = $total_weight;
    }

    $weight = $chargeable_weight;
    $weight = number_format($weight, 2, '.', '');
    // Fecha Contrato

    $company_user = User::where('id', \Auth::id())->first();
    $company_setting = CompanyUser::where('id', \Auth::user()->company_user_id)->first();
    $typeCurrency = 'USD';
    $idCurrency = 149;

    $currency_name = '';

    if ($company_setting->currency_id != null) {
        $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();

        $typeCurrency = $company_setting->currency->alphacode;
        $idCurrency = $company_setting->currency_id;
    }

    $currencies = Currency::all()->pluck('alphacode', 'id');

    //Settings de la compañia
    $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();

    $weight = number_format($weight, 2, '.', '');
    $collectionRate = new Collection();
    // Rates LCL

    $arreglo = RateLcl::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
        $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
            $a->where('user_id', '=', $user_id);
        })->orDoesntHave('contract_user_restriction');
    })->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
        $q->whereHas('contract_company_restriction', function ($b) use ($company_id) {
            $b->where('company_id', '=', $company_id);
        })->orDoesntHave('contract_company_restriction');
    })->whereHas('contract', function ($q) use ($company_user_id, $dateSince, $dateUntil, $company_setting) {
        if ($company_setting->future_dates == 1) {
            $q->where(function ($query) use ($dateSince) {
                $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
            })->where('company_user_id', '=', $company_user_id);
        } else {
            $q->where(function ($query) use ($dateSince, $dateUntil) {
                $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
            })->where('company_user_id', '=', $company_user_id);
        }
    })->get();

    $collectionGeneral = new Collection();

    foreach ($arreglo as $data) {

        $tt = $data->transit_time;
        $va = $data->via;

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
        $collectionGloOrig = new Collection();
        $collectionGloDest = new Collection();
        $collectionGloFreight = new Collection();
        $collectionRate = new Collection();

        $dataGOrig = array();
        $dataGDest = array();
        $dataGFreight = array();

        $dataOrig = array();
        $dataDest = array();
        $dataFreight = array();

        $rateC = $this->ratesCurrency($data->currency->id, $data->currency->alphacode);

        $typeCurrencyFreight = $data->currency->alphacode;
        $idCurrencyFreight = $data->currency->id;

        $subtotal = 0;

        $inlandDestiny = new Collection();
        $inlandOrigin = new Collection();
        $totalChargeOrig = 0;
        $totalChargeDest = 0;
        $totalInland = 0;

        if ($total_weight != null) {

            $simple = 'show active';
            $paquete = '';
            $subtotalT = $weight * $data->uom;
            $totalT = ($weight * $data->uom) / $rateC;
            $priceRate = $data->uom;

            if ($subtotalT < $data->minimum) {
                $subtotalT = $data->minimum;
                $totalT = $subtotalT / $rateC;
                if ($weight < 1) {
                    $weightP = 1;
                } else {
                    $weightP = $weight;
                }

                $priceRate = $data->minimum / $weightP;
                $priceRate = number_format($priceRate, 2, '.', '');
            }

            $totalT = number_format($totalT, 2, '.', '');
            $totalFreight += $totalT;
            $totalRates += $totalT;

            $array = array('type' => 'Ocean Freight', 'cantidad' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode, 'subtotal' => $subtotalT, 'total' => $totalT . " " . $typeCurrency, 'idCurrency' => $data->currency_id);

            $collectionRate->push($array);
            $collectionGeneral->put('rates', $array);

        }

        $data->setAttribute('rates', $collectionRate);

        $orig_port = array($data->origin_port);
        $dest_port = array($data->destiny_port);
        $carrier[] = $data->carrier_id;

        // id de los port  ALL
        array_push($orig_port, 1485);
        array_push($dest_port, 1485);
        // id de los carrier ALL
        $carrier_all = 26;
        array_push($carrier, $carrier_all);
        // Id de los paises
        array_push($origin_country, 250);
        array_push($destiny_country, 250);

        //Calculation type
        $arrayBlHblShip = array('1', '2', '3', '16', '18', '20', '21'); // id  calculation type 1 = HBL , 2=  Shipment , 3 = BL , 16 per set
        $arraytonM3 = array('4', '11', '17'); //  calculation type 4 = Per ton/m3
        $arraytonCompli = array('6', '7', '12', '13'); //  calculation type 4 = Per ton/m3
        $arrayPerTon = array('5', '10'); //  calculation type 5 = Per  TON
        $arrayPerKG = array('9'); //  calculation type 5 = Per  TON
        $arrayPerPack = array('14'); //  per package
        $arrayPerPallet = array('15'); //  per pallet
        $arrayPerM3 = array('19'); //  per m3

        // Local charges
        $localChar = LocalChargeLcl::where('contractlcl_id', '=', $data->contractlcl_id)->whereHas('localcharcarrierslcl', function ($q) use ($carrier) {
            $q->whereIn('carrier_id', $carrier);
        })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
            $query->whereHas('localcharportslcl', function ($q) use ($orig_port, $dest_port) {
                $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
            })->orwhereHas('localcharcountrieslcl', function ($q) use ($origin_country, $destiny_country) {
                $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
            });
        })->with('localcharportslcl.portOrig', 'localcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

        foreach ($localChar as $local) {

            $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);
            $rateC = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
            //Totales peso y volumen
            $totalW = $request->input('total_weight') / 1000;
            $totalV = $request->input('total_volume');

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
                        if ($company_setting->origincharge == '1') {
                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;
                                    // MARKUP
                                    //$markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_name' => $local->surcharge->name, 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloOrig = array_merge($arregloOrig, $markupBL);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                    $collectionOrig->push($arregloOrigin);

                                }
                            }
                        }
                        if ($company_setting->destinationcharge == '1') {
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;
                                    // MARKUP
                                    //   $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_name' => $local->surcharge->name, 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //   $arregloDest = array_merge($arregloDest, $markupBL);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                    $collectionDest->push($arregloDest);
                                }
                            }
                        }
                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {
                                $subtotal_local = $local->ammount;
                                $totalAmmount = $local->ammount / $rateC;

                                // MARKUP
                                // $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                //$totalAmmount =  $local->ammout  / $rateMount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloPC = array('surcharge_name' => $local->surcharge->name, 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                //   $arregloPC = array_merge($arregloPC, $markupBL);

                                $collectionFreight->push($arregloPC);

                                // ARREGLO GENERAL 99

                                $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                $collectionFreight->push($arregloFreight);
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

                        if ($chargesOrigin != null) {
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
                                //$markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalOrigin += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloOrigTonM3 = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                // $arregloOrigTonM3 = array_merge($arregloOrigTonM3, $markupTonM3);

                                $collectionOrig->push($arregloOrigTonM3);

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                $collectionOrig->push($arregloOrigin);
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

                                //$markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalDestiny += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloDest = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                // $arregloDest = array_merge($arregloDest, $markupTonM3);

                                $collectionDest->push($arregloDest);

                                // Arreglo 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                $collectionDest->push($arregloDest);
                            }
                        }
                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {
                                $subtotal_local = $ton_weight * $local->ammount;
                                $totalAmmount = ($ton_weight * $local->ammount) / $rateC;
                                $mont = $local->ammount;
                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = $subtotal_local / $rateC;
                                    $mont = $local->minimum / $ton_weight;
                                    $mont = number_format($mont, 2, '.', '');
                                    $cantidadT = 1;
                                }

                                // MARKUP
                                //$markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                //$totalAmmount =  $local->ammout  / $rateMount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloPC = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                //  $arregloPC = array_merge($arregloPC, $markupTonM3);

                                $collectionFreight->push($arregloPC);

                                // Arreglo 99

                                $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                $collectionFreight->push($arregloFreight);
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

                        if ($chargesOrigin != null) {
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
                                // $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalOrigin += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloOrigTon = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                //$arregloOrigTon = array_merge($arregloOrigTon, $markupTON);
                                $collectionOrig->push($arregloOrigTon);

                                // ARREGLO GENERAL 99

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionOrig->push($arregloOrigin);
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
                                //$markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalDestiny += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloDest = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                //$arregloDest = array_merge($arregloDest, $markupTON);

                                $collectionDest->push($arregloDest);

                                // ARREGLO GENERAL 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionDest->push($arregloDest);
                            }
                        }

                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {

                                $subtotal_local = $totalW * $local->ammount;
                                $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                $mont = $local->ammount;
                                $unidades = $this->unidadesTON($totalW);
                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = $subtotal_local / $rateC;
                                    $mont = $local->minimum / $totalW;
                                    $mont = number_format($mont, 2, '.', '');
                                }

                                // MARKUP
                                // $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloPC = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                //$arregloPC = array_merge($arregloPC, $markupTON);

                                $collectionFreight->push($arregloPC);
                                // ARREGLO GENERAL 99

                                $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                $collectionFreight->push($arregloFreight);
                            }
                        }
                    }
                }
            }

            if (in_array($local->calculationtypelcl_id, $arraytonCompli)) {

                foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                    if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                        if ($chargesOrigin != null) {
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
                                //   $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                //$totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                $arregloOrig = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'montoOrig' => $totalAmmount);
                                // $arregloOrig = array_merge($arregloOrig, $markupTONM3);
                                $dataOrig[] = $arregloOrig;

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $dataOrig[] = $arregloOrigin;
                                //$collectionOrig->push($arregloOrigin);

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

                                //$markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'montoOrig' => $totalAmmount);
                                //$arregloDest = array_merge($arregloDest, $markupTONM3);
                                $dataDest[] = $arregloDest;

                                // ARREGLO 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $dataDest[] = $arregloDest;
                                //$collectionDest->push($arregloDest);

                            }
                        }

                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {
                                if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                    if ($local->calculationtypelcl_id == '13') {
                                        $totalV = ceil($totalV);
                                    }
                                    $subtotal_local = $totalV * $local->ammount;
                                    $totalAmmount = ($totalV * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $totalV;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateC;
                                        $mont = $local->minimum / $totalV;
                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                } else {
                                    if ($local->calculationtypelcl_id == '12') {
                                        $totalW = ceil($totalW);
                                    }
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    if ($totalW > 1) {
                                        $unidades = $totalW;
                                    } else {
                                        $unidades = '1';
                                    }

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateC;
                                        if ($totalW < 1) {
                                            $mont = $local->minimum * $totalW;
                                        } else {
                                            $mont = $local->minimum / $totalW;
                                        }
                                    }
                                }
                                // Markup
                                // $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloPC = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                //$arregloPC = array_merge($arregloPC, $markupTONM3);
                                $dataFreight[] = $arregloPC;

                                // ARREGLO 99

                                $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                $dataFreight[] = $arregloFreight;
                                //$collectionFreight->push($arregloFreight);

                            }
                        }
                    }
                }
            }

            if (in_array($local->calculationtypelcl_id, $arrayPerKG)) {

                foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                    if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                        if ($chargesOrigin != null) {
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
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalOrigin += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $arregloOrigKg = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                //$arregloOrigKg = array_merge($arregloOrigKg, $markupKG);
                                $collectionOrig->push($arregloOrigKg);

                                // ARREGLO GENERAL 99

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionOrig->push($arregloOrigin);
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
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalDestiny += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $arregloDestKg = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                // $arregloDestKg = array_merge($arregloDestKg, $markupKG);

                                $collectionDest->push($arregloDestKg);

                                // ARREGLO GENERAL 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionDest->push($arregloDest);
                            }
                        }

                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {

                                $subtotal_local = $totalW * $local->ammount;
                                $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                $mont = $local->ammount;
                                $unidades = $totalW;

                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = ($totalW * $subtotal_local) / $rateC;
                                    $unidades = $subtotal_local / $totalW;
                                }
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                // MARKUP
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                //$totalAmmount =  $local->ammout  / $rateMount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloFreightKg = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                //$arregloFreightKg = array_merge($arregloFreightKg, $markupKG);

                                $collectionFreight->push($arregloFreightKg);
                                // ARREGLO GENERAL 99

                                $arregloFreightKg = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

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
                        if ($chargesOrigin != null && $package_cantidad != 0) {
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
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalOrigin += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloOrigpack = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                //$arregloOrigpack = array_merge($arregloOrigpack, $markupKG);
                                $collectionOrig->push($arregloOrigpack);

                                // ARREGLO GENERAL 99

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionOrig->push($arregloOrigin);
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
                                // $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalDestiny += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $arregloDestPack = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                // $arregloDestPack = array_merge($arregloDestPack, $markupKG);

                                $collectionDest->push($arregloDestPack);

                                // ARREGLO GENERAL 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionDest->push($arregloDest);
                            }
                        }

                        if ($chargesFreight != null && $package_cantidad != 0) {
                            if ($local->typedestiny_id == '3') {

                                $subtotal_local = $package_cantidad * $local->ammount;
                                $totalAmmount = ($package_cantidad * $local->ammount) / $rateC;
                                $mont = $local->ammount;
                                $unidades = $package_cantidad;

                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = ($package_cantidad * $subtotal_local) / $rateC;
                                    $unidades = $subtotal_local / $package_cantidad;
                                }
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                // MARKUP
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                //$totalAmmount =  $local->ammout  / $rateMount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloFreightPack = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                //$arregloFreightPack = array_merge($arregloFreightPack, $markupKG);

                                $collectionFreight->push($arregloFreightPack);
                                // ARREGLO GENERAL 99

                                $arregloFreightPack = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

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
                        if ($chargesOrigin != null && $pallet_cantidad != 0) {
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
                                // $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalOrigin += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloOrigpallet = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                //   $arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
                                $collectionOrig->push($arregloOrigpallet);

                                // ARREGLO GENERAL 99

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionOrig->push($arregloOrigin);
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
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalDestiny += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $arregloDestPallet = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                //$arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

                                $collectionDest->push($arregloDestPallet);

                                // ARREGLO GENERAL 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionDest->push($arregloDest);
                            }
                        }

                        if ($chargesFreight != null && $pallet_cantidad != 0) {
                            if ($local->typedestiny_id == '3') {

                                $subtotal_local = $pallet_cantidad * $local->ammount;
                                $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateC;
                                $mont = $local->ammount;
                                $unidades = $pallet_cantidad;

                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateC;
                                }
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                // MARKUP
                                // $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                //$totalAmmount =  $local->ammout  / $rateMount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloFreightPallet = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                // $arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);

                                $collectionFreight->push($arregloFreightPallet);
                                // ARREGLO GENERAL 99

                                $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                $collectionFreight->push($arregloFreight);
                            }
                        }
                    }
                }
            }

            if (in_array($local->calculationtypelcl_id, $arrayPerM3)) {

                foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                    if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                        if ($request->input('total_volume') != null) {
                            $totalVol = $request->input('total_volume');
                        } else {
                            $totalVol = $request->input('total_volume_pkg');
                        }

                        if ($chargesOrigin != null && $totalVol != 0) {
                            if ($local->typedestiny_id == '1') {

                                $subtotal_local = $totalVol * $local->ammount;
                                $totalAmmount = ($totalVol * $local->ammount) / $rateMount;
                                $mont = $local->ammount;
                                $unidades = $totalVol;

                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = ($totalVol * $subtotal_local) / $rateMount;
                                }

                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                // MARKUP
                                //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                $totalOrigin += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                $arregloOrigpallet = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                $collectionOrig->push($arregloOrigpallet);

                                // ARREGLO GENERAL 99

                                $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionOrig->push($arregloOrigin);
                            }
                        }

                        if ($chargesDestination != null && $totalVol != 0) {
                            if ($local->typedestiny_id == '2') {
                                $subtotal_local = $totalVol * $local->ammount;
                                $totalAmmount = ($totalVol * $local->ammount) / $rateMount;
                                $mont = $local->ammount;
                                $unidades = $totalVol;

                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = ($totalVol * $subtotal_local) / $rateMount;
                                }
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                // MARKUP

                                $totalDestiny += $totalAmmount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $arregloDestPallet = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                $collectionDest->push($arregloDestPallet);

                                // ARREGLO GENERAL 99

                                $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                $collectionDest->push($arregloDest);
                            }
                        }

                        if ($chargesFreight != null && $totalVol != 0) {
                            if ($local->typedestiny_id == '3') {

                                $subtotal_local = $totalVol * $local->ammount;
                                $totalAmmount = ($totalVol * $local->ammount) / $rateC;
                                $mont = $local->ammount / $rateC;
                                $unidades = $totalVol;

                                if ($subtotal_local < $local->minimum) {
                                    $subtotal_local = $local->minimum;
                                    $totalAmmount = ($totalVol * $subtotal_local) / $rateC;
                                }
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                //$totalAmmount =  $local->ammout  / $rateMount;
                                $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloFreightVol = array('surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                $collectionFreight->push($arregloFreightVol);
                                // ARREGLO GENERAL 99

                                $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00,   'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                $collectionFreight->push($arregloFreight);
                            }
                        }
                    }
                }
            }

        } // Fin del calculo de los local charges

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

        $totalFreightOrig = $totalFreight;

        $rateTotal = $this->ratesCurrency($data->currency->id, $typeCurrency);
        $totalFreight = $totalFreight / $rateTotal;
        $totalFreight = number_format($totalFreight, 2, '.', '');

        $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
        $totalQuoteSin = number_format($totalQuote, 2, ',', '');

        if ($chargesDestination == null && $chargesOrigin == null) {

            $totalQuote = $totalFreightOrig;
            $data->setAttribute('quoteCurrency', $data->currency->alphacode);
        } else {
            $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
            $data->setAttribute('quoteCurrency', $typeCurrency);
        }

        if (!empty($collectionOrig)) {
            $collectionOrig = $this->OrdenarCollectionLCL($collectionOrig);
        }

        if (!empty($collectionDest)) {
            $collectionDest = $this->OrdenarCollectionLCL($collectionDest);
        }

        if (!empty($collectionFreight)) {
            $collectionFreight = $this->OrdenarCollectionLCL($collectionFreight);
        }

      

        //General information

        $information['information'] = array('id' => $data->id, 'uom' => $data->uom, 'minimum' => $data->minimum, 'transit_time' => $data->transit_time, 'via' => $data->via, 'created_at' => $data->created_at, 'updated_at' => $data->updated_at);
        $information['information']['origin_port'] = array('id' => $data->port_origin->id, 'name' => $data->port_origin->display_name, 'code' => $data->port_origin->code, 'coordinates' => $data->port_destiny->coordinates);
        $information['information']['destination_port'] = array('id' => $data->port_destiny->id, 'name' => $data->port_destiny->display_name, 'code' => $data->port_destiny->code, 'coordinates' => $data->port_destiny->coordinates);
        $information['information']['carrier'] = array('id' => $data->carrier->id, 'name' => $data->carrier->name, 'code' => $data->carrier->uncode);

        $collectionGeneral->put('general', $information);
        $collectionGeneral->put('charges_origin', $collectionOrig);
        $collectionGeneral->put('charges_destination', $collectionDest);
        $collectionGeneral->put('charges_freight', $collectionFreight);

        /*
        $data->setAttribute('localOrig', $collectionOrig);
        $data->setAttribute('localDest', $collectionDest);
        $data->setAttribute('localFreight', $collectionFreight);
         */

        $data->setAttribute('freightCharges', $FreightCharges);
        $data->setAttribute('totalFreight', $totalFreight);
        $data->setAttribute('totalFreightOrig', $totalFreightOrig);

        $data->setAttribute('totalrates', $totalRates);
        $data->setAttribute('totalOrigin', $totalOrigin);
        $data->setAttribute('totalDestiny', $totalDestiny);

        $data->setAttribute('totalQuote', $totalQuote);
        // INLANDS

        $data->setAttribute('totalChargeOrig', $totalChargeOrig);
        $data->setAttribute('totalChargeDest', $totalChargeDest);

        //Total quote atributes

        //    $data->setAttribute('rateCurrency', $data->currency->alphacode);
        //   $data->setAttribute('totalQuoteSin', $totalQuoteSin);
        //    $data->setAttribute('idCurrency', $idCurrency);
        // SCHEDULES
        //  $data->setAttribute('schedulesFin', "");

        // Ordenar las colecciones

    }

    $arreglo = $arreglo->sortBy('totalQuote');

    return response()->json($collectionGeneral);
/*

$mixSearch = array();
$company_setting = CompanyUser::where('id', \Auth::user()->company_user_id)->first();

if (isset($request->contact_id) && isset($request->company_id_quote)) {
$contact = contact::find($request->contact_id);

$contact_cliente = $contact->first_name . ' ' . $contact->last_name;
$company_cliente = $companies[$request->company_id_quote];
} else {
$contact_cliente = null;
$company_cliente = null;
}*/
}