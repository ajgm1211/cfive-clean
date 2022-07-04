<?php

namespace App\Observers;

use App\DeliveryType;

class DeliveryTypeObserver
{
    /**
     * Handle the delivery type "created" event.
     *
     * @param  \App\DeliveryType  $deliveryType
     * @return void
     */
    public function created(DeliveryType $deliveryType)
    {
        cache()->forget('data_delivery_types');
    }

    /**
     * Handle the delivery type "updated" event.
     *
     * @param  \App\DeliveryType  $deliveryType
     * @return void
     */
    public function updated(DeliveryType $deliveryType)
    {
        cache()->forget('data_delivery_types');
    }

    /**
     * Handle the delivery type "deleted" event.
     *
     * @param  \App\DeliveryType  $deliveryType
     * @return void
     */
    public function deleted(DeliveryType $deliveryType)
    {
        cache()->forget('data_delivery_types');
    }

    /**
     * Handle the delivery type "restored" event.
     *
     * @param  \App\DeliveryType  $deliveryType
     * @return void
     */
    public function restored(DeliveryType $deliveryType)
    {
        //
    }

    /**
     * Handle the delivery type "force deleted" event.
     *
     * @param  \App\DeliveryType  $deliveryType
     * @return void
     */
    public function forceDeleted(DeliveryType $deliveryType)
    {
        //
    }
}
