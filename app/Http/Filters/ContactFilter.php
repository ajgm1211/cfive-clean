<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ContactFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['id', 'first_name', 'last_name', 'created_at'];
    protected $filter_by_relations = [ 'company_id' ];
    protected $with = ['company'];
}