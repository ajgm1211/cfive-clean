<?php

namespace App;

use App\Http\Filters\SaleTermCodeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleTermCode extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'name', 'description', 'company_user_id'];

    /**
     * Scope a query to only include contracts by authenticated users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id);
    }

    /**
     * scopeFilter
     *
     * @param  mixed $builder
     * @param  mixed $request
     * @return void
     */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new SaleTermCodeFilter($request, $builder))->filter();
    }

    /* Duplicate Inland Model instance with relations */
    public function duplicate()
    {
        $new_sale_term_code = $this->replicate();
        $new_sale_term_code->save();

        return $new_sale_term_code;
    }
}
