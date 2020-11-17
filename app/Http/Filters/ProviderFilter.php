<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ProviderFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['name','description'];
    protected $filter_by_relations = [ ];
    protected $with = [];

}