<?php

namespace App\Http\Traits;

use Illuminate\Support\Collection as Collection;

trait EntityTrait {

    public function processArray($array)
    {
        $array = array_filter($array, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return $array;
    }

}