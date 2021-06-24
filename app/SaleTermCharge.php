<?php

namespace App;

use App\Http\Filters\SaleTermChargeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleTermCharge extends Model
{
    protected $fillable = ['id', 'sale_term_id', 'amount', 'calculation_type_id', 'currency_id', 'sale_term_code_id', 'total'];

    protected $casts = [
        'total' => 'array',
    ];

    /**
     * Scope a query to only include charges by sale term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBySaleTerm($query, $sale_term_id)
    {
        return $query->where('sale_term_id', '=', $sale_term_id);
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
        return (new SaleTermChargeFilter($request, $builder))->filter();
    }

    /* Duplicate Inland Model instance with relations */
    public function duplicate()
    {
        $new_sale_term_charge = $this->replicate();
        $new_sale_term_charge->save();

        return $new_sale_term_charge;
    }

    public function sale_term()
    {
        return $this->belongsTo('App\SaleTermV3');
    }

    public function sale_term_code()
    {
        return $this->hasOne('App\SaleTermCode', 'id', 'sale_term_code_id');
    }

    public function calculation_type()
    {
        return $this->hasOne('App\CalculationType', 'id', 'calculation_type_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Currency', 'id', 'currency_id');
    }

    public function jsonTotal($data)
    {
        $containers = Container::where('gp_container_id', $this->sale_term->group_container_id)->get();

        $total = [];
        
        foreach ($containers as $container) {
            ${'rates_' . $container->code} = 'rates_' . $container->code;
            $total['c' . $container->code] = $data->${'rates_' . $container->code} ?? 0;
        }
        
        $this->update(['total' => $total]);
    }
}
