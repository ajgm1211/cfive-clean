<?php

namespace App\Observers;

use App\CargoKind;

class CargoKindObserver
{
    /**
     * Handle the cargo kind "created" event.
     *
     * @param  \App\CargoKind  $cargoKind
     * @return void
     */
    public function created(CargoKind $cargoKind)
    {
        cache()->forget('data_kind_of_cargo');
    }

    /**
     * Handle the cargo kind "updated" event.
     *
     * @param  \App\CargoKind  $cargoKind
     * @return void
     */
    public function updated(CargoKind $cargoKind)
    {
        cache()->forget('data_kind_of_cargo');
    }

    /**
     * Handle the cargo kind "deleted" event.
     *
     * @param  \App\CargoKind  $cargoKind
     * @return void
     */
    public function deleted(CargoKind $cargoKind)
    {
        cache()->forget('data_kind_of_cargo');
    }

    /**
     * Handle the cargo kind "restored" event.
     *
     * @param  \App\CargoKind  $cargoKind
     * @return void
     */
    public function restored(CargoKind $cargoKind)
    {
        //
    }

    /**
     * Handle the cargo kind "force deleted" event.
     *
     * @param  \App\CargoKind  $cargoKind
     * @return void
     */
    public function forceDeleted(CargoKind $cargoKind)
    {
        //
    }
}
