<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ContractFilter extends AbstractFilter
{
    protected $paginate = 10;
    //protected $filter_by = [ 'name', 'status' ];
    protected $filter_by_relations = [ 'carriers.carrier__name', 'gpContainer__name'];
    protected $default_filter_by = [];
    protected $with = [
    	'carriers.carrier', 
    	'contract_company_restriction.company', 
    	'contract_user_restriction.user', 
    	'direction', 
    	'companyUser', 
    	'gpContainer'
    ];

}

