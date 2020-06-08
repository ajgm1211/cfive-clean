<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class LocalChargeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ ];
    protected $filter_by_relations = [ 'localcharports.portOrig__display_name', 'localcharports.portDest__display_name', 'localcharcountries.countryOrig__name', 'localcharcountries.countryDest__name' ];
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
    	'localcharports.portDest'
    	];

}