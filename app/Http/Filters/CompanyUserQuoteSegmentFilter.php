<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class CompanyUserQuoteSegmentFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [];
    protected $with = [];
}