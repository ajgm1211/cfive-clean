<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class CompanyFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['id', 'business_name', 'phone', 'email', 'address', 'tax_number', 'created_at'];
    //protected $filter_by_relations = [ 'company_user_id' ];
    protected $with = [];
}