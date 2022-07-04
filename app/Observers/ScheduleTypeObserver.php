<?php

namespace App\Observers;

use App\ScheduleType;

class ScheduleTypeObserver
{
    /**
     * Handle the schedule type "created" event.
     *
     * @param  \App\ScheduleType  $scheduleType
     * @return void
     */
    public function created(ScheduleType $scheduleType)
    {
        cache()->forget('data_schedule_types');
    }

    /**
     * Handle the schedule type "updated" event.
     *
     * @param  \App\ScheduleType  $scheduleType
     * @return void
     */
    public function updated(ScheduleType $scheduleType)
    {
        cache()->forget('data_schedule_types');
    }

    /**
     * Handle the schedule type "deleted" event.
     *
     * @param  \App\ScheduleType  $scheduleType
     * @return void
     */
    public function deleted(ScheduleType $scheduleType)
    {
        cache()->forget('data_schedule_types');
    }

    /**
     * Handle the schedule type "restored" event.
     *
     * @param  \App\ScheduleType  $scheduleType
     * @return void
     */
    public function restored(ScheduleType $scheduleType)
    {
        //
    }

    /**
     * Handle the schedule type "force deleted" event.
     *
     * @param  \App\ScheduleType  $scheduleType
     * @return void
     */
    public function forceDeleted(ScheduleType $scheduleType)
    {
        //
    }
}
