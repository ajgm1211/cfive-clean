<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class CompanyGroupFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [];
    protected $filter_by_relations = [ 'company_user_id' ];
    protected $with = [];
}