<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class PriceLevelFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['prices_level_id', 'created_at'];
    protected $filter_by_relations = [ 'company_user_id' ];
    protected $with = [];
}