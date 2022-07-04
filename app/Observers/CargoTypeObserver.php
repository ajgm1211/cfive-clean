<?php

namespace App\Observers;

use App\CargoType;

class CargoTypeObserver
{
    /**
     * Handle the cargo type "created" event.
     *
     * @param  \App\CargoType  $cargoType
     * @return void
     */
    public function created(CargoType $cargoType)
    {
        cache()->forget('data_cargo_types');
    }

    /**
     * Handle the cargo type "updated" event.
     *
     * @param  \App\CargoType  $cargoType
     * @return void
     */
    public function updated(CargoType $cargoType)
    {
        cache()->forget('data_cargo_types');
    }

    /**
     * Handle the cargo type "deleted" event.
     *
     * @param  \App\CargoType  $cargoType
     * @return void
     */
    public function deleted(CargoType $cargoType)
    {
        cache()->forget('data_cargo_types');
    }

    /**
     * Handle the cargo type "restored" event.
     *
     * @param  \App\CargoType  $cargoType
     * @return void
     */
    public function restored(CargoType $cargoType)
    {
        //
    }

    /**
     * Handle the cargo type "force deleted" event.
     *
     * @param  \App\CargoType  $cargoType
     * @return void
     */
    public function forceDeleted(CargoType $cargoType)
    {
        //
    }
}
