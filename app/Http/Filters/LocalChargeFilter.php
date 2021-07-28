<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class LocalChargeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['ammount'];
    protected $filter_by_relations = ['currency__alphacode', 'calculationtype__name', 'surcharge__name', 'typedestiny__description', 'localcharcarriers.carrier__name', 'localcharports.portOrig__display_name', 'localcharports.portDest__display_name', 'localcharcountries.countryOrig__name', 'localcharcountries.countryDest__name'];
    protected $with = [
        'surcharge',
        'typedestiny',
        'contract',
        'calculationtype',
        'currency',
        'localcharcarriers.carrier',
        'localcharcountries.countryOrig',
        'localcharcountries.countryDest',
        'localcharports.portOrig',
        'localcharports.portDest',
        ];
}
