<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ContractFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ 'name', 'company_user_id' ];
    protected $default_filter_by = [ 'company_user_id' ];
    protected $with = ['carriers.carrier', 'direction', 'companyUser'];

}

