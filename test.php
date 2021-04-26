//############ Global Charges   ####################
            /*
            $globalChar = GlobalChargeLcl::where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrierslcl', function ($q) use ($carrier) {
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
            $rateC = $this->ratesCurrency($global->currency->id, $data->currency->alphacode);

            if ($request->input('total_weight') != null) {
            $totalW = $request->input('total_weight') / 1000;
            $totalV = $request->input('total_volume');
            $totalWeight = $request->input('total_weight');
            } else {
            $totalW = $request->input('total_weight_pkg') / 1000;
            $totalV = $request->input('total_volume_pkg');
            $totalWeight = $request->input('total_weight');
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
            $arregloOrig = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => '-', 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrig = array_merge($arregloOrig, $markupBL);
            //$origGlo["origin"] = $arregloOrig;
            $collectionOrig->push($arregloOrig);
            // $collectionGloOrig->push($arregloOrig);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => '1');

            $collectionOrig->push($arregloOrigin);
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
            $arregloDest = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => '1', 'monto' => $global->ammount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDest = array_merge($arregloDest, $markupBL);

            $collectionDest->push($arregloDest);

            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => '1');

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null) {
            if ($global->typedestiny_id == '3') {
            $subtotal_global = $global->ammount;
            $totalAmmount = $global->ammount / $rateC;

            // MARKUP
            $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalFreight += $totalAmmount;
            $FreightCharges += $totalAmmount;
            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloFreight = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => '-', 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreight = array_merge($arregloFreight, $markupBL);

            $collectionFreight->push($arregloFreight);

            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => '1');

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
            $arregloOrig = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrig = array_merge($arregloOrig, $markupTonM3);

            $collectionOrig->push($arregloOrig);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $cantidadT);

            $collectionOrig->push($arregloOrigin);
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
            $arregloDest = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDest = array_merge($arregloDest, $markupTonM3);

            $collectionDest->push($arregloDest);

            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $cantidadT);

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null) {
            if ($global->typedestiny_id == '3') {
            $subtotal_global = $ton_weight * $global->ammount;
            $totalAmmount = ($ton_weight * $global->ammount) / $rateC;
            $mont = $global->ammount;
            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = $subtotal_global / $rateC;
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
            $arregloFreight = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreight = array_merge($arregloFreight, $markupTonM3);

            $collectionFreight->push($arregloFreight);

            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

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
            $arregloOrig = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrig = array_merge($arregloOrig, $markupTON);

            $collectionOrig->push($arregloOrig);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionOrig->push($arregloOrigin);
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
            $arregloDest = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDest = array_merge($arregloDest, $markupTON);
            $collectionDest->push($arregloDest);
            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null) {
            if ($global->typedestiny_id == '3') {

            $subtotal_global = $totalW * $global->ammount;
            $totalAmmount = ($totalW * $global->ammount) / $rateC;
            $mont = $global->ammount;
            $unidades = $this->unidadesTON($totalW);
            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = $subtotal_global / $rateC;
            $mont = $global->minimum / $totalW;

            $mont = number_format($mont, 2, '.', '');
            }
            // MARKUP
            $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalFreight += $totalAmmount;
            $FreightCharges += $totalAmmount;
            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloFreight = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreight = array_merge($arregloFreight, $markupTON);
            $collectionFreight->push($arregloFreight);

            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

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
            $arregloOrig = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrig = array_merge($arregloOrig, $markupTONM3);
            $dataGOrig[] = $arregloOrig;

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $dataGOrig[] = $arregloOrigin;
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
            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDest = array_merge($arregloDest, $markupTONM3);
            $dataGDest[] = $arregloDest;

            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

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
            $totalAmmount = ($totalV * $global->ammount) / $rateC;
            $mont = $global->ammount;
            $unidades = $totalV;
            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = $subtotal_global / $rateC;
            $mont = $global->minimum / $totalV;
            $mont = number_format($mont, 2, '.', '');
            }
            } else {
            if ($global->calculationtypelcl_id == '12') {
            $totalW = ceil($totalW);
            }
            $subtotal_global = $totalW * $global->ammount;
            $totalAmmount = ($totalW * $global->ammount) / $rateC;
            $mont = $global->ammount;
            if ($totalW > 1) {
            $unidades = $totalW;
            } else {
            $unidades = '1';
            }

            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = $subtotal_global / $rateC;
            $mont = $global->minimum / $totalW;
            $mont = number_format($mont, 2, '.', '');
            }
            }
            // MARKUP

            $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreight = array_merge($arregloFreight, $markupTONM3);
            $dataGFreight[] = $arregloFreight;

            // ARREGLO GENERAL 99
            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
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
            // dd($subtotal_global,$global->minimum);

            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = $subtotal_global / $rateMountG;
            $unidades = $subtotal_global / $totalWeight;
            }
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalOrigin += $totalAmmount;
            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloOrigKg = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrigKg = array_merge($arregloOrigKg, $markupKG);

            $collectionOrig->push($arregloOrigKg);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionOrig->push($arregloOrigin);
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

            $arregloDestKg = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDestKg = array_merge($arregloDestKg, $markupKG);
            $collectionDest->push($arregloDestKg);
            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null) {
            if ($global->typedestiny_id == '3') {

            $subtotal_global = $totalWeight * $global->ammount;
            $totalAmmount = ($totalWeight * $global->ammount) / $rateC;
            $mont = "";
            $unidades = $totalWeight;

            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = ($totalWeight * $subtotal_global) / $rateC;
            $unidades = $subtotal_global / $totalWeight;
            }
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalFreight += $totalAmmount;
            $FreightCharges += $totalAmmount;
            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloFreightKg = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreightKg = array_merge($arregloFreightKg, $markupKG);
            $collectionFreight->push($arregloFreightKg);

            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

            $collectionFreight->push($arregloFreight);
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
            $arregloOrigPack = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrigPack = array_merge($arregloOrigPack, $markupKG);

            $collectionOrig->push($arregloOrigPack);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionOrig->push($arregloOrigin);
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

            $arregloDestKg = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDestPack = array_merge($arregloDestPack, $markupKG);
            $collectionDest->push($arregloDestPack);
            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null && $package_cantidad != '0') {
            if ($global->typedestiny_id == '3') {

            $subtotal_global = $package_cantidad * $global->ammount;
            $totalAmmount = ($package_cantidad * $global->ammount) / $rateC;
            $mont = "";
            $unidades = $package_cantidad;

            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = ($package_cantidad * $subtotal_global) / $rateC;
            $unidades = $subtotal_global / $package_cantidad;
            }
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalFreight += $totalAmmount;
            $FreightCharges += $totalAmmount;
            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloFreightPack = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreightPack = array_merge($arregloFreightPack, $markupKG);
            $collectionFreight->push($arregloFreightPack);

            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

            $collectionFreight->push($arregloFreight);
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
            $arregloOrigPallet = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrigPallet = array_merge($arregloOrigPallet, $markupKG);

            $collectionOrig->push($arregloOrigPallet);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionOrig->push($arregloOrigin);
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

            $arregloDestPallet = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDestPallet = array_merge($arregloDestPallet, $markupKG);
            $collectionDest->push($arregloDestPallet);
            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null && $pallet_cantidad != '0') {
            if ($global->typedestiny_id == '3') {

            $subtotal_global = $pallet_cantidad * $global->ammount;
            $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateC;
            $mont = "";
            $unidades = $pallet_cantidad;

            if ($subtotal_global < $global->minimum) {
            $subtotal_global = $global->minimum;
            $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateC;
            }
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalFreight += $totalAmmount;
            $FreightCharges += $totalAmmount;
            $subtotal_global = number_format($subtotal_global, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloFreightPallet = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);
            $collectionFreight->push($arregloFreightPallet);

            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

            $collectionFreight->push($arregloFreight);
            }
            }
            }
            }
            }

            if (in_array($global->calculationtypelcl_id, $arrayPerM3)) {

            foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
            if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

            if ($request->input('total_volume') != null) {
            $totalVol = $request->input('total_volume');
            } else {
            $totalVol = $request->input('total_volume_pkg');
            }

            if ($chargesOrigin != null && $totalVol != 0) {
            if ($global->typedestiny_id == '1') {

            $subtotal_local = $totalVol * $global->ammount;
            $totalAmmount = ($totalVol * $global->ammount) / $rateMountG;
            $mont = $global->ammount;
            $unidades = $totalVol;

            if ($subtotal_local < $global->minimum) {
            $subtotal_local = $global->minimum;
            $totalAmmount = ($totalVol * $subtotal_local) / $rateMountG;
            }

            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalOrigin += $totalAmmount;
            $subtotal_local = number_format($subtotal_local, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            $arregloOrigpallet = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
            $collectionOrig->push($arregloOrigpallet);

            // ARREGLO GENERAL 99

            $arregloOrigin = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionOrig->push($arregloOrigin);
            }
            }

            if ($chargesDestination != null && $totalVol != 0) {
            if ($global->typedestiny_id == '2') {
            $subtotal_local = $totalVol * $global->ammount;
            $totalAmmount = ($totalVol * $global->ammount) / $rateMountG;
            $mont = $global->ammount;
            $unidades = $totalVol;

            if ($subtotal_local < $global->minimum) {
            $subtotal_local = $global->minimum;
            $totalAmmount = ($totalVol * $subtotal_local) / $rateMountG;
            }
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            $totalDestiny += $totalAmmount;
            $subtotal_local = number_format($subtotal_local, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $arregloDestPallet = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id,  'montoOrig' => $totalAmmount);
            $arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

            $collectionDest->push($arregloDestPallet);

            // ARREGLO GENERAL 99

            $arregloDest = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id,  'cantidad' => $unidades);

            $collectionDest->push($arregloDest);
            }
            }

            if ($chargesFreight != null && $totalVol != 0) {
            if ($global->typedestiny_id == '3') {

            $subtotal_local = $totalVol * $global->ammount;
            $totalAmmount = ($totalVol * $global->ammount) / $rateC;
            $mont = $global->ammount;
            $unidades = $totalVol;

            if ($subtotal_local < $global->minimum) {
            $subtotal_local = $global->minimum;
            $totalAmmount = ($totalVol * $subtotal_local) / $rateC;
            }
            $totalAmmount = number_format($totalAmmount, 2, '.', '');

            // MARKUP
            $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

            //$totalAmmount =  $global->ammout  / $rateMount;
            $subtotal_local = number_format($subtotal_local, 2, '.', '');
            $totalAmmount = number_format($totalAmmount, 2, '.', '');
            $totalFreight += $totalAmmount;
            $FreightCharges += $totalAmmount;
            $arregloFreightVol = array( 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
            $arregloFreightVol = array_merge($arregloFreightVol, $markupKG);

            $collectionFreight->push($arregloFreightVol);
            // ARREGLO GENERAL 99

            $arregloFreight = array( 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '0', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
            }
            }
            }
            }
            }
            }

             */

            //############ Fin Global Charges ##################