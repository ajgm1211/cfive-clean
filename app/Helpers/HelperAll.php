<?php

//app/Helpers/Envato/User.php

namespace App\Helpers;

use App\Currency;
use App\GroupContainer;

class HelperAll
{
    public static function addOptionSelect($dataAll, $id, $name)
    {
        $data = [null => 'Please Select'];
        foreach ($dataAll as $dataRun) {
            $data[$dataRun[$id]] = $dataRun[$name];
        }

        return $data;
    }

    public static function currencyJoin($statusCurrency, $currency_bol, $val_ps, $curr_ps)
    {
        $data = null;
        if ($statusCurrency == 2) { // Valores junto con la moneda
            if ($currency_bol == true) {
                $currencyObj = Currency::find($curr_ps);
                $currency_val = $currencyObj->alphacode;
            } else {
                $currency_val = $curr_ps;
            }
            $data = $val_ps . ' ' . $currency_val;
        } else { // Moneda especificada en el Select o columna
            $data = $val_ps;
        }

        return $data;
    }

    public static function validatorError($data)
    {
        $result = null;
        $Arr = null;
        $Arr = explode('_', $data);
        if (count($Arr) <= 1) {
            $result = $Arr[0];
        } else {
            $result = $Arr[0] . ' (error)';
        }

        return $result;
    }

    public static function validatorErrorWitdColor($data)
    {
        $result = null;
        $Arr = null;
        $Arr = explode('_', $data);
        $resul = [];
        if (count($Arr) <= 1) {
            $result['value'] = $Arr[0];
            $result['color'] = 'green';
        } else {
            $result['value'] = $Arr[0] . ' (error)';
            $result['color'] = 'red';
        }

        return $result;
    }

    public static function LoadHearderDataTable($equiment_id, $type)
    {
        if (strnatcasecmp($type, 'rates') == 0) {
            $equiments = GroupContainer::with('containers')->find($equiment_id);
            //dd($equiment->containers->pluck('code'));
            $datajson = json_decode($equiments->data, true);
            $equiment = [];
            // Head Datatable <th>
            $equiment = ['id' => $equiment_id, 'color' => $datajson['color'], 'name' => $equiments->name, 'thead' => [null, 'Origin', 'Destiny', 'Carrier']];
            foreach ($equiments->containers as $containers) {
                array_push($equiment['thead'], $containers->code);
            }
            array_push($equiment['thead'], 'Currency');
            array_push($equiment['thead'], 'Option');
            // Head Datatable json{}
            $json_array = [
                ['data' => 'origin', 'name' => 'origin'],
                ['data' => 'destiny', 'name' => 'destiny'],
                ['data' => 'carrier', 'name' => 'carrier'],
            ];
            foreach ($equiments->containers as $containers) {
                array_push($json_array, ['data' => 'C' . $containers->code, 'name' => 'C' . $containers->code]);
            }
            array_push($json_array, ['data' => 'currency', 'name' => 'currency']);
            array_push($json_array, ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]);
            $equiment['columns'] = json_encode($json_array);
        }

        return $equiment;
    }

    public static function LoadHearderContaniers($equiment_id, $type)
    {
        if (strnatcasecmp($type, 'rates') == 0) {
            $equiments = GroupContainer::with('containers')->find($equiment_id);
            //dd($equiment->containers->pluck('code'));
            $datajson = json_decode($equiments->data, true);
            $equiment = [];
            // Head Datatable <th>
            $equiment = ['id' => $equiment_id, 'color' => $datajson['color'], 'name' => $equiments->name, 'thead' => []];
            foreach ($equiments->containers as $containers) {
                array_push($equiment['thead'], $containers->code);
            }
        }

        return $equiment;
    }

    public static function statusColorRq($status)
    {
        $color = null;
        if (strnatcasecmp($status, 'Pending') == 0) {
            $color = '#f81538';
        } elseif (strnatcasecmp($status, 'Processing') == 0) {
            $color = '#5527f0';
        } elseif (strnatcasecmp($status, 'Review') == 0) {
            $color = '#e07000';
        } elseif (strnatcasecmp($status, 'Imp Finished') == 0) {
            $color = '#431b02';
        } elseif (strnatcasecmp($status, 'Clarification needed') == 0) {
            $color = '#fc94af';
        } elseif (strnatcasecmp($status, 'Done') == 0) {
            $color = '#04950f';
        }

        return $color;
    }
    public static function statusColorHarbor($hierarchy, $name = '')
    {

        $color = array();
        if ($hierarchy == 'parent') {
            $color[0] = '#f81538';
            $color[1] = 'false';
            $color[2] = '';

        } else if ($hierarchy == 'child') {
            $color[0] = '#5527f0;';
            $color[1] = 'true';
            $color[2] = 'data-toggle="tooltip" data-placement="top" title="' . $name . '"';

        } else {
            $color[0] = '#04950f';
            $color[1] = 'false';
            $color[2] = '';
        }
        return $color;
    }

    public static function removeAccent($fileName)
    {
        
        //Ahora reemplazamos las letras
        $fileName = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $fileName);
    
        $fileName = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $fileName);
    
        $fileName = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $fileName);
    
        $fileName = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $fileName);
    
        $fileName = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $fileName);
    
        $fileName = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $fileName
        );
    
        return $fileName;
    }
}
