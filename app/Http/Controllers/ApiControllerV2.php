<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\GroupContainer;
use App\Http\Resources\GetContractApiResource;
use App\Http\Traits\MixPanelTrait;
use App\Http\Traits\UtilTrait;
use App\Rate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ApiControllerV2 extends Controller
{

    use UtilTrait, MixPanelTrait;

    /**
     * Get contract's details by parameters.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getContract(Request $request)
    {
        try {
            //Check if were received all needed parameters
            if (!$request->carrier || !$request->container || !$request->direction || !$request->since || !$request->until) {
                return response()->json(['message' => 'There are missing parameters. You must send direction, carrier, since, until and container'], 400);
            }

            //Getting ocean freight rates from DB
            $rates = $this->getRates($request);

            return GetContractApiResource::collection($rates);

        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'The requested container type does not exist',
                ], 404);
            } else {
                return response()->json([
                    'message' => 'There was an error trying to get the data',
                ], 500);
            }
        }
    }

    /**
     * 
     * Fecth rates from DB
     * 
     * @param mixed $data
     * 
     * @return eloquent collection
     */
    public function getRates($data)
    {
        //Setting variables from request
        $dateSince = $data->input('since');
        $dateUntil = $data->input('until');
        $reference = $data->reference;
        $company_user = \Auth::user()->CompanyUser;
        $carrier = $this->getCarrier($data->carrier);
        $direction = $data->input('direction') == 3 ? array(1, 2, 3) : array($data->input('direction'));
        $code = GroupContainer::where('id', $data->input('container'))->orWhere('name', $data->input('container'))->firstOrFail();

        //Setting order by default
        $orderBy = 'desc';

        if($data->order) {
            $orderBy = $data->order;
        }

        //Setting pagination size
        $paginate = 50;

        if ($data->paginate && $data->paginate <= 100) {
            $paginate = $data->paginate;
        }

        $rates = Rate::whereIn('carrier_id', $carrier)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user, $direction, $code, $reference) {
            if ($company_user->future_dates == 1) {
                $q->where(function ($query) use ($dateSince) {
                    $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                })->when($reference, function ($query, $name) {
                    return $query->where('name', 'LIKE', '%' . $name . '%');
                })->where('company_user_id', '=', $company_user->id)->whereIn('direction_id', $direction)->where('status', '!=', 'incomplete')->where('gp_container_id', $code->id);
            } else {
                $q->where(function ($query) use ($dateSince, $dateUntil) {
                    $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                })->when($reference, function ($query, $name) {
                    return $query->where('name', 'LIKE', '%' . $name . '%');
                })->where('company_user_id', '=', $company_user->id)->whereIn('direction_id', $direction)->where('status', '!=', 'incomplete')->where('gp_container_id', $code->id);
            }
        })->orderBy('contract_id',$orderBy)->paginate($paginate);

        return $rates;
    }

    /**
     * 
     * Get carrier data from DB
     * 
     * @param mixed $carrierUrl
     * 
     * @return eloquent collection
     */
    public function getCarrier($carrierUrl)
    {
        if ($carrierUrl == "all") {
            $carriers = Carrier::all()->pluck('id')->toArray();
        } else {
            $carriers = Carrier::where('name', $carrierUrl)
            ->orWhere('uncode', $carrierUrl)
            ->orWhere('scac', $carrierUrl)
            ->orWhere('id', $carrierUrl)
            ->pluck('id')->toArray();
        }

        return $carriers;
    }
}
