<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ContractLclFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['name', 'status', 'validity', 'expire'];
    protected $filter_by_relations = ['carriers.carrier__name', 'direction__name'];
    protected $default_filter_by = [];
    protected $with = [
        'carriers.carrier',
        'direction',
        'companyUser',
    ];
}
