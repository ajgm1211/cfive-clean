<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $origin_ports = $this->origin_harbor()->get();
        $destiny_ports = $this->destination_harbor()->get();
        $origin_array = [];
        $destiny_array = [];

        foreach($origin_ports as $item){
            array_push($origin_array, $item->display_name);
        }

        foreach($destiny_ports as $item){
            array_push($destiny_array, $item->display_name);
        }

        if(isset($this->user_id)){
            $this->user->setAttribute('fullname', $this->user->fullname);
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'quote_id' => $this->quote_id,
            'company_id' => $this->company,
            'status' => $this->status_quote()->first(),
            'rates' => $this->rates_v2,
            'origin' => $origin_array ?? '--',
            'destiny' => $destiny_array ?? '--',
            'rates' => $this->rates_v2,
            'user_id' => $this->user,
            'created_at' => date('Y-m-d H:m:s', strtotime($this->created_at)),
        ];
    }
}
