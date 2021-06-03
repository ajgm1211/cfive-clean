<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class LocalChargeLclFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['ammount'];
    protected $filter_by_relations = ['currency__alphacode', 'calculationtypelcl__name', 'surcharge__name', 'typedestiny__description', 'localcharcarrierslcl.carrier__name', 'localcharportslcl.portOrig__display_name', 'localcharportslcl.portDest__display_name', 'localcharcountrieslcl.countryOrig__name', 'localcharcountrieslcl.countryDest__name'];
    protected $with = [
        'surcharge',
        'typedestiny',
        'contract',
        'calculationtypelcl',
        'currency',
        'localcharcarrierslcl.carrier',
        'localcharcountrieslcl.countryOrig',
        'localcharcountrieslcl.countryDest',
        'localcharportslcl.portOrig',
        'localcharportslcl.portDest',
        ];
}
