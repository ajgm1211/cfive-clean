<?php

namespace App\Observers;

use App\PaymentCondition;

class PaymentConditionObserver
{
    /**
     * Handle the payment condition "created" event.
     *
     * @param  \App\PaymentCondition  $paymentCondition
     * @return void
     */
    public function created(PaymentCondition $paymentCondition)
    {
        cache()->forget('data_payment_conditions');
    }

    /**
     * Handle the payment condition "updated" event.
     *
     * @param  \App\PaymentCondition  $paymentCondition
     * @return void
     */
    public function updated(PaymentCondition $paymentCondition)
    {
        cache()->forget('data_payment_conditions');
    }

    /**
     * Handle the payment condition "deleted" event.
     *
     * @param  \App\PaymentCondition  $paymentCondition
     * @return void
     */
    public function deleted(PaymentCondition $paymentCondition)
    {
        cache()->forget('data_payment_conditions');
    }

    /**
     * Handle the payment condition "restored" event.
     *
     * @param  \App\PaymentCondition  $paymentCondition
     * @return void
     */
    public function restored(PaymentCondition $paymentCondition)
    {
        //
    }

    /**
     * Handle the payment condition "force deleted" event.
     *
     * @param  \App\PaymentCondition  $paymentCondition
     * @return void
     */
    public function forceDeleted(PaymentCondition $paymentCondition)
    {
        //
    }
}
