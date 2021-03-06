<?php

namespace App;

use App\Http\Filters\TransitTimeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TransitTime extends Model
{
    protected $fillable = [
        'origin_id',
        'destination_id',
        'carrier_id',
        'transit_time',
        'service_id',
        'via',
    ];

    /**
     * Return an App\Harbor model associated to the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function origin()
    {
        return $this->belongsTo('App\Harbor');
    }

    /**
     * Return an App\Harbor model associated to the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function destination()
    {
        return $this->belongsTo('App\Harbor');
    }

    /**
     * Return a App\Carrier model associated to the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    /**
     * Return a App\DestinationType model associated to the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function service()
    {
        return $this->belongsTo('App\DestinationType');
    }

    /**
     * Scope a query filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Http\Request $request;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new TransitTimeFilter($request, $builder))->filter();
    }

    /**
     * Return True if Transit Time already exists.
     *
     * @param  \Illuminate\Http\Request $request;
     * @return bool
     */
    public static function scheduleExists($request, $transit_time = null)
    {
        $origin_id = $request->input('origin');
        $destination_id = $request->input('destination');
        $carrier_id = $request->input('carrier');

        $query = self::where([
                ['origin_id', '=', $origin_id],
                ['destination_id', '=', $destination_id],
                ['carrier_id', '=', $carrier_id],
            ]);

        if ($transit_time) {
            $query->where('id', '<>', $transit_time->id);
        }

        return $query->get()->count() > 0;
    }
}
