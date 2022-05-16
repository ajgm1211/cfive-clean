<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class FailedContactFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['id', 'first_name', 'last_name', 'phone', 'email', 'position', 'company_id', 'company_user_id'];
    //protected $filter_by_relations = [ 'company_id' ];
    protected $with = [];
}