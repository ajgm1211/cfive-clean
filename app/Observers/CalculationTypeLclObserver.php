<?php

namespace App\Observers;

use App\CalculationTypeLcl;

class CalculationTypeLclObserver
{
    /**
     * Handle the calculation type lcl "created" event.
     *
     * @param  \App\CalculationTypeLcl  $calculationTypeLcl
     * @return void
     */
    public function created(CalculationTypeLcl $calculationTypeLcl)
    {
        cache()->forget('data_calculationtypeslcl');
    }

    /**
     * Handle the calculation type lcl "updated" event.
     *
     * @param  \App\CalculationTypeLcl  $calculationTypeLcl
     * @return void
     */
    public function updated(CalculationTypeLcl $calculationTypeLcl)
    {
        cache()->forget('data_calculationtypeslcl');
    }

    /**
     * Handle the calculation type lcl "deleted" event.
     *
     * @param  \App\CalculationTypeLcl  $calculationTypeLcl
     * @return void
     */
    public function deleted(CalculationTypeLcl $calculationTypeLcl)
    {
        cache()->forget('data_calculationtypeslcl');
    }

    /**
     * Handle the calculation type lcl "restored" event.
     *
     * @param  \App\CalculationTypeLcl  $calculationTypeLcl
     * @return void
     */
    public function restored(CalculationTypeLcl $calculationTypeLcl)
    {
        //
    }

    /**
     * Handle the calculation type lcl "force deleted" event.
     *
     * @param  \App\CalculationTypeLcl  $calculationTypeLcl
     * @return void
     */
    public function forceDeleted(CalculationTypeLcl $calculationTypeLcl)
    {
        //
    }
}
