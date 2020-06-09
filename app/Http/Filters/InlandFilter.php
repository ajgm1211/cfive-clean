<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class InlandFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ 'provider' ];
    protected $default_filter_by = [ ];
    protected $with = [ 'companyUser', 'inland_type', 'direction', 'gpContainer', 'inland_company_restriction.company_user'];

}

