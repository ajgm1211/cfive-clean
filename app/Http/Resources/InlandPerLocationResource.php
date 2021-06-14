<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InlandPerLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $containers=json_encode($this->container_json);

        $data = [
            'container' => $containers,
            'currency' => $this->currency_id,
            'harbor' => $this->harbor_id,
            'inland' => $this->inland_id,
            'location' => $this->location_id,
            'type' => $this->type,
        ];

        return $data;
    }
}