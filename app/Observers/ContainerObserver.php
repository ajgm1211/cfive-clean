<?php

namespace App\Observers;

use App\Container;

class ContainerObserver
{
    /**
     * Handle the container "created" event.
     *
     * @param  \App\Container  $container
     * @return void
     */
    public function created(Container $container)
    {
        cache()->forget('data_containers');
    }

    /**
     * Handle the container "updated" event.
     *
     * @param  \App\Container  $container
     * @return void
     */
    public function updated(Container $container)
    {
        cache()->forget('data_containers');
    }

    /**
     * Handle the container "deleted" event.
     *
     * @param  \App\Container  $container
     * @return void
     */
    public function deleted(Container $container)
    {
        cache()->forget('data_containers');
    }

    /**
     * Handle the container "restored" event.
     *
     * @param  \App\Container  $container
     * @return void
     */
    public function restored(Container $container)
    {
        //
    }

    /**
     * Handle the container "force deleted" event.
     *
     * @param  \App\Container  $container
     * @return void
     */
    public function forceDeleted(Container $container)
    {
        //
    }
}
